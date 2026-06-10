<?php

namespace Tests\Feature\Ai;

use App\Ai\Agents\SanadChat;
use App\Ai\Tools\GetCrisisResources;
use App\Ai\Tools\GetMyScreeningResult;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\ScreeningSession;
use App\Models\SessionAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;
use Laravel\Ai\Tools\Request;
use Tests\TestCase;

class GetMyScreeningResultToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_is_exposed_with_snake_case_name(): void
    {
        $this->assertSame('get_my_screening_result', (new GetMyScreeningResult)->name());
    }

    public function test_tool_accepts_no_parameters(): void
    {
        // The empty schema is the security boundary: the model cannot supply a session id.
        $this->assertSame([], (new GetMyScreeningResult)->schema(new JsonSchemaTypeFactory));
    }

    public function test_sanad_chat_registers_both_tools(): void
    {
        $tools = iterator_to_array((new SanadChat)->forScreeningSession(123)->tools());

        $this->assertCount(2, $tools);
        $this->assertInstanceOf(GetCrisisResources::class, $tools[0]);
        $this->assertInstanceOf(GetMyScreeningResult::class, $tools[1]);
    }

    public function test_handle_returns_item_level_breakdown_with_scores(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 12,
            'gad7_score' => 9,
            'combined_severity' => 'moderate',
        ]);
        $this->answer($session, 'Feeling down, depressed, or hopeless', 'Nearly every day', 3, order: 1);

        $output = (string) (new GetMyScreeningResult($session->id, 'en'))->handle(new Request);

        $this->assertStringContainsString('PHQ-9: 12/27', $output);
        $this->assertStringContainsString('GAD-7: 9/21', $output);
        $this->assertStringContainsString('overall severity: moderate', $output);
        $this->assertStringContainsString('Feeling down, depressed, or hopeless: "Nearly every day" (score 3)', $output);
    }

    public function test_handle_orders_answers_by_question_order(): void
    {
        $session = ScreeningSession::factory()->create();
        $this->answer($session, 'Second symptom', 'Some answer', 1, order: 5);
        $this->answer($session, 'First symptom', 'Some answer', 2, order: 0);

        $output = (string) (new GetMyScreeningResult($session->id, 'en'))->handle(new Request);

        $this->assertLessThan(
            strpos($output, 'Second symptom'),
            strpos($output, 'First symptom'),
        );
    }

    public function test_handle_includes_free_text_answers(): void
    {
        $session = ScreeningSession::factory()->create();
        $question = Question::factory()->create(['text_en' => 'Anything else to share?', 'order' => 0]);
        SessionAnswer::factory()->create([
            'screening_session_id' => $session->id,
            'question_id' => $question->id,
            'question_option_id' => null,
            'free_text' => 'I feel exhausted all the time.',
        ]);

        $output = (string) (new GetMyScreeningResult($session->id, 'en'))->handle(new Request);

        $this->assertStringContainsString('Free-text: "I feel exhausted all the time."', $output);
    }

    public function test_handle_never_leaks_another_sessions_answers(): void
    {
        $mine = ScreeningSession::factory()->create();
        $this->answer($mine, 'My own symptom', 'My answer', 1, order: 0);

        $other = ScreeningSession::factory()->create();
        $this->answer($other, 'Another students secret symptom', 'Their answer', 3, order: 0);

        $output = (string) (new GetMyScreeningResult($mine->id, 'en'))->handle(new Request);

        $this->assertStringContainsString('My own symptom', $output);
        $this->assertStringNotContainsString('Another students secret symptom', $output);
    }

    public function test_handle_returns_arabic_breakdown(): void
    {
        $session = ScreeningSession::factory()->create(['combined_severity' => 'moderate']);
        $question = Question::factory()->create([
            'text_en' => 'Feeling down',
            'text_ar' => 'الشعور بالإحباط',
            'order' => 0,
        ]);
        $option = QuestionOption::factory()->create([
            'question_id' => $question->id,
            'label_en' => 'Nearly every day',
            'label_ar' => 'تقريباً كل يوم',
            'value' => 3,
        ]);
        SessionAnswer::factory()->create([
            'screening_session_id' => $session->id,
            'question_id' => $question->id,
            'question_option_id' => $option->id,
        ]);

        $output = (string) (new GetMyScreeningResult($session->id, 'ar'))->handle(new Request);

        $this->assertStringContainsString('الشعور بالإحباط', $output);
        $this->assertStringContainsString('تقريباً كل يوم', $output);
        $this->assertStringNotContainsString('Feeling down', $output);
    }

    public function test_handle_returns_invite_message_when_no_screening_linked(): void
    {
        $english = (string) (new GetMyScreeningResult(null, 'en'))->handle(new Request);
        $arabic = (string) (new GetMyScreeningResult(null, 'ar'))->handle(new Request);

        $this->assertStringContainsString('has not completed a screening', $english);
        $this->assertStringContainsString('لم يكمل', $arabic);
    }

    private function answer(ScreeningSession $session, string $questionText, string $optionLabel, int $value, int $order): void
    {
        $question = Question::factory()->create(['text_en' => $questionText, 'order' => $order]);
        $option = QuestionOption::factory()->create([
            'question_id' => $question->id,
            'label_en' => $optionLabel,
            'value' => $value,
        ]);
        SessionAnswer::factory()->create([
            'screening_session_id' => $session->id,
            'question_id' => $question->id,
            'question_option_id' => $option->id,
        ]);
    }
}
