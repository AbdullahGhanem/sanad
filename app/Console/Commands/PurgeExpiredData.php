<?php

namespace App\Console\Commands;

use App\Models\ChatMessage;
use App\Models\ConsentRecord;
use App\Models\CrisisEvent;
use App\Models\ScreeningSession;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class PurgeExpiredData extends Command
{
    protected $signature = 'app:purge-expired-data {--dry-run : Report what would be deleted without deleting}';

    protected $description = 'Delete anonymous records past their configured retention window';

    /**
     * Retention config key => model. Screening sessions are purged first so their
     * answers and linked chat messages cascade away atomically.
     *
     * @var array<string, class-string<Model>>
     */
    private const TARGETS = [
        'screening_sessions' => ScreeningSession::class,
        'chat_messages' => ChatMessage::class,
        'crisis_events' => CrisisEvent::class,
        'consent_records' => ConsentRecord::class,
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $purged = [];

        foreach (self::TARGETS as $key => $model) {
            $days = config("retention.{$key}");

            if ($days === null) {
                $this->line("Skipping {$key} (retention disabled).");

                continue;
            }

            $cutoff = now()->subDays((int) $days);
            $query = $model::query()->where('created_at', '<', $cutoff);

            $count = $dryRun ? $query->count() : $query->delete();

            if ($count > 0) {
                $purged[$key] = $count;
            }

            $verb = $dryRun ? 'Would purge' : 'Purged';
            $this->line("{$verb} {$count} {$key} older than {$days} days.");
        }

        if ($purged !== [] && ! $dryRun) {
            activity('retention')
                ->withProperties(['purged' => $purged])
                ->log('Purged expired data');
        }

        return self::SUCCESS;
    }
}
