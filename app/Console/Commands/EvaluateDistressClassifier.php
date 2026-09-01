<?php

namespace App\Console\Commands;

use App\Ai\Agents\DistressAnalyzer;
use Illuminate\Console\Command;
use SplFileObject;

class EvaluateDistressClassifier extends Command
{
    protected $signature = 'app:evaluate-distress
        {csv : Labelled dataset with columns id,text,label (see tools/research-paper/distress-eval-template.csv)}
        {--language=ar : Language hint passed to the DistressAnalyzer agent}
        {--out= : Write per-row predictions to this CSV for error analysis}';

    protected $description = 'Run the DistressAnalyzer agent over a clinician-labelled CSV and report precision, recall and F1 per severity class';

    /** @var list<string> */
    private const CLASSES = ['minimal', 'mild', 'moderate', 'moderately_severe', 'severe'];

    public function handle(): int
    {
        $rows = $this->readRows((string) $this->argument('csv'));

        if ($rows === null) {
            return self::FAILURE;
        }

        $confusion = array_fill_keys(self::CLASSES, array_fill_keys(self::CLASSES, 0));
        $predictions = [];
        $bar = $this->output->createProgressBar(count($rows));

        foreach ($rows as $row) {
            $response = (new DistressAnalyzer((string) $this->option('language')))
                ->prompt("Analyze the following text for mental health distress indicators:\n\n{$row['text']}");

            $predicted = in_array($response['severity'] ?? null, self::CLASSES, true) ? $response['severity'] : 'minimal';
            $confusion[$row['label']][$predicted]++;
            $predictions[] = $row + [
                'predicted' => $predicted,
                'confidence' => (float) ($response['confidence'] ?? 0),
                'themes' => implode('|', $response['themes'] ?? []),
                'correct' => (int) ($predicted === $row['label']),
            ];
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->report($confusion, count($rows));

        if ($out = $this->option('out')) {
            $this->writePredictions((string) $out, $predictions);
            $this->info("Per-row predictions written to {$out}");
        }

        return self::SUCCESS;
    }

    /**
     * @return list<array{id: string, text: string, label: string}>|null
     */
    private function readRows(string $path): ?array
    {
        if (! is_readable($path)) {
            $this->error("Cannot read {$path}");

            return null;
        }

        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::READ_AHEAD | SplFileObject::DROP_NEW_LINE);

        $header = null;
        $rows = [];
        $errors = [];

        foreach ($file as $line => $fields) {
            if ($header === null) {
                $header = array_map(fn ($field) => strtolower(trim((string) $field)), $fields);

                if (! in_array('text', $header, true) || ! in_array('label', $header, true)) {
                    $this->error('CSV header must contain "text" and "label" columns.');

                    return null;
                }

                continue;
            }

            $record = array_combine($header, array_pad($fields, count($header), ''));
            $label = strtolower(trim((string) $record['label']));
            $text = trim((string) $record['text']);

            if ($text === '') {
                continue;
            }

            if (! in_array($label, self::CLASSES, true)) {
                $errors[] = sprintf('Row %d: unknown label "%s" (expected one of %s)', $line + 1, $label, implode(', ', self::CLASSES));

                continue;
            }

            $rows[] = ['id' => (string) ($record['id'] ?? $line), 'text' => $text, 'label' => $label];
        }

        if ($errors !== []) {
            foreach ($errors as $error) {
                $this->error($error);
            }

            return null;
        }

        if ($rows === []) {
            $this->error('No labelled rows found.');

            return null;
        }

        return $rows;
    }

    /**
     * @param  array<string, array<string, int>>  $confusion
     */
    private function report(array $confusion, int $total): void
    {
        $correct = array_sum(array_map(fn (string $class) => $confusion[$class][$class], self::CLASSES));
        $this->line(sprintf('Agreement: %.1f%% (%d/%d)', $correct / $total * 100, $correct, $total));
        $this->newLine();

        $rows = [];
        $weighted = ['precision' => 0.0, 'recall' => 0.0, 'f1' => 0.0];

        foreach (self::CLASSES as $class) {
            $tp = $confusion[$class][$class];
            $support = array_sum($confusion[$class]);
            $predictedAs = array_sum(array_column($confusion, $class));

            $precision = $predictedAs > 0 ? $tp / $predictedAs : 0.0;
            $recall = $support > 0 ? $tp / $support : 0.0;
            $f1 = ($precision + $recall) > 0 ? 2 * $precision * $recall / ($precision + $recall) : 0.0;

            $rows[] = [$class, number_format($precision, 2), number_format($recall, 2), number_format($f1, 2), $support];

            $weighted['precision'] += $precision * $support / $total;
            $weighted['recall'] += $recall * $support / $total;
            $weighted['f1'] += $f1 * $support / $total;
        }

        $rows[] = ['Weighted avg', number_format($weighted['precision'], 2), number_format($weighted['recall'], 2), number_format($weighted['f1'], 2), $total];

        $this->table(['Class', 'Precision', 'Recall', 'F1', 'n'], $rows);

        $this->newLine();
        $this->line('Confusion matrix (rows = clinician label, columns = model prediction):');
        $this->table(
            ['', ...self::CLASSES],
            array_map(fn (string $class) => [$class, ...array_values($confusion[$class])], self::CLASSES),
        );
    }

    /**
     * @param  list<array<string, mixed>>  $predictions
     */
    private function writePredictions(string $path, array $predictions): void
    {
        $handle = fopen($path, 'w');
        fputcsv($handle, ['id', 'text', 'label', 'predicted', 'confidence', 'themes', 'correct']);

        foreach ($predictions as $row) {
            fputcsv($handle, [$row['id'], $row['text'], $row['label'], $row['predicted'], $row['confidence'], $row['themes'], $row['correct']]);
        }

        fclose($handle);
    }
}
