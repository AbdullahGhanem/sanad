<?php

namespace App\Ai\Tools;

use App\Models\ScreeningSession;
use App\Models\SessionAnswer;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetMyScreeningResult implements Tool
{
    public function __construct(
        private ?int $screeningSessionId = null,
        private string $language = 'en',
    ) {}

    /**
     * Get the tool's name as exposed to the model.
     */
    public function name(): string
    {
        return 'get_my_screening_result';
    }

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return "Retrieve the detailed item-level breakdown of THIS student's own most recent mental-health "
            .'screening — every question, the answer they gave, its score, and any free-text they wrote. '
            .'Call this when the student asks about their results, scores, or which specific symptoms they '
            .'reported. Returns only the current student\'s screening; it takes no arguments.';
    }

    /**
     * Get the tool's schema definition.
     *
     * The tool intentionally accepts no parameters: the screening session is fixed at
     * construction time from a trusted source, so the model cannot request another
     * student's data.
     *
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        if (! $this->screeningSessionId) {
            return $this->noScreeningMessage();
        }

        $session = ScreeningSession::query()
            ->with(['answers.question', 'answers.option'])
            ->find($this->screeningSessionId);

        if (! $session) {
            return $this->noScreeningMessage();
        }

        $items = $session->answers
            ->sortBy(fn (SessionAnswer $answer) => $answer->question?->order ?? PHP_INT_MAX)
            ->map(fn (SessionAnswer $answer): string => $this->formatAnswer($answer))
            ->implode("\n");

        return $this->header($session)."\n".$items;
    }

    /**
     * Build the score/severity header line.
     */
    private function header(ScreeningSession $session): string
    {
        $phq9 = $session->phq9_score ?? '—';
        $gad7 = $session->gad7_score ?? '—';
        $severity = $session->combined_severity ?? 'unknown';

        return $this->language === 'ar'
            ? "أحدث فحص للطالب — الاكتئاب (PHQ-9): {$phq9}/27، القلق (GAD-7): {$gad7}/21، الشدة العامة: {$severity}. تفاصيل الإجابات:"
            : "The student's most recent screening — PHQ-9: {$phq9}/27, GAD-7: {$gad7}/21, overall severity: {$severity}. Item-by-item answers:";
    }

    /**
     * Format a single answer into a readable line.
     */
    private function formatAnswer(SessionAnswer $answer): string
    {
        $question = $this->localized($answer->question?->text_ar, $answer->question?->text_en)
            ?? ($this->language === 'ar' ? 'سؤال' : 'Question');

        if ($answer->option) {
            $label = $this->localized($answer->option->label_ar, $answer->option->label_en) ?? '';
            $scoreLabel = $this->language === 'ar' ? 'الدرجة' : 'score';

            return "- {$question}: \"{$label}\" ({$scoreLabel} {$answer->option->value})";
        }

        if (filled($answer->free_text)) {
            $prefix = $this->language === 'ar' ? 'إجابة نصية' : 'Free-text';

            return "- {$question} — {$prefix}: \"{$answer->free_text}\"";
        }

        return "- {$question}: —";
    }

    /**
     * Pick the localized value, falling back to the other language.
     */
    private function localized(?string $arabic, ?string $english): ?string
    {
        return $this->language === 'ar'
            ? ($arabic ?? $english)
            : ($english ?? $arabic);
    }

    /**
     * Message used when no screening is linked to this conversation.
     */
    private function noScreeningMessage(): string
    {
        return $this->language === 'ar'
            ? 'لم يكمل هذا الطالب فحصاً مرتبطاً بهذه المحادثة. اقترح عليه إجراء الفحص للحصول على دعم مخصص.'
            : 'This student has not completed a screening linked to this conversation. Invite them to take '
                .'the screening for personalised support.';
    }
}
