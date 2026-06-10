<?php

namespace App\Ai\Tools;

use App\Enums\CopingTheme;
use App\Models\CopingExercise;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Collection;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SuggestCopingExercise implements Tool
{
    private const LIMIT = 3;

    public function __construct(
        private string $language = 'en',
    ) {}

    /**
     * Get the tool's name as exposed to the model.
     */
    public function name(): string
    {
        return 'suggest_coping_exercise';
    }

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Suggest one or more evidence-based coping exercises (breathing, grounding, cognitive, '
            .'behavioral, mindfulness) tailored to the emotional theme the student is experiencing. '
            .'Call this when the student would benefit from a concrete, practical strategy. Share the '
            .'returned steps with the student; do not invent your own exercises.';
    }

    /**
     * Get the tool's schema definition.
     *
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        $themes = implode(', ', CopingTheme::values());

        return [
            'theme' => $schema->string()->description(
                "The emotional theme to address. One of: {$themes}. Omit for general-purpose exercises."
            ),
        ];
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $theme = $this->normalizeTheme($request['theme'] ?? null);

        $exercises = $this->resolveExercises($theme);

        if ($exercises->isEmpty()) {
            return $this->noExercisesMessage();
        }

        return $exercises
            ->map(fn (CopingExercise $exercise): string => $this->formatExercise($exercise))
            ->implode("\n\n");
    }

    /**
     * Resolve exercises for the theme, falling back to general then any.
     *
     * @return Collection<int, CopingExercise>
     */
    private function resolveExercises(?string $theme): Collection
    {
        if ($theme) {
            $matches = $this->query()->forTheme($theme)->get();

            if ($matches->isNotEmpty()) {
                return $matches;
            }
        }

        if ($theme !== CopingTheme::General->value) {
            $general = $this->query()->forTheme(CopingTheme::General->value)->get();

            if ($general->isNotEmpty()) {
                return $general;
            }
        }

        return $this->query()->get();
    }

    /**
     * Base query for active exercises, ordered and limited.
     *
     * @return \Illuminate\Database\Eloquent\Builder<CopingExercise>
     */
    private function query(): \Illuminate\Database\Eloquent\Builder
    {
        return CopingExercise::query()->active()->ordered()->limit(self::LIMIT);
    }

    /**
     * Normalize a model-supplied theme to a known value, or null.
     */
    private function normalizeTheme(?string $theme): ?string
    {
        if (! filled($theme)) {
            return null;
        }

        $theme = strtolower(trim($theme));

        return in_array($theme, CopingTheme::values(), true) ? $theme : null;
    }

    /**
     * Format a single exercise for the model to relay.
     */
    private function formatExercise(CopingExercise $exercise): string
    {
        $title = $exercise->getTitle($this->language);
        $line = "**{$title}** ({$exercise->type}";

        if ($exercise->duration_minutes) {
            $unit = $this->language === 'ar' ? 'دقائق' : 'min';
            $line .= ", ~{$exercise->duration_minutes} {$unit}";
        }

        $line .= ')';

        if ($summary = $exercise->getSummary($this->language)) {
            $line .= "\n{$summary}";
        }

        return $line."\n".$exercise->getSteps($this->language);
    }

    /**
     * Message used when no exercises are configured.
     */
    private function noExercisesMessage(): string
    {
        return $this->language === 'ar'
            ? 'لا توجد تمارين مسجلة حاليًا. قدّم للطالب دعمًا عاطفيًا واقترح التواصل مع مختص.'
            : 'No exercises are currently configured. Offer the student emotional support and suggest '
                .'reaching out to a professional.';
    }
}
