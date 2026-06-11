<?php

namespace Tests\Feature\Audit;

use App\Ai\Agents\SanadChat;
use App\Filament\Resources\Activities\ActivityResource;
use App\Jobs\StreamChatResponse;
use App\Models\AiProviderSetting;
use App\Models\CrisisKeyword;
use App\Models\KnowledgeArticle;
use App\Models\User;
use App\Services\ContextInjectionService;
use App\Services\ResponseGuardrailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_clinical_config_changes_are_audited(): void
    {
        $keyword = CrisisKeyword::create(['phrase' => 'kill myself', 'language' => 'en', 'is_active' => true]);
        $keyword->update(['phrase' => 'end it all']);

        $activity = Activity::where('log_name', 'crisis-config')
            ->where('subject_type', CrisisKeyword::class)
            ->where('event', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertSame('end it all', $activity->attribute_changes['attributes']['phrase']);
    }

    public function test_knowledge_article_audit_excludes_embedding_vectors(): void
    {
        KnowledgeArticle::factory()->withEmbedding([0.1, 0.2, 0.3])->create(['title_en' => 'Sleep Hygiene']);

        $activity = Activity::where('subject_type', KnowledgeArticle::class)->latest()->first();

        $this->assertNotNull($activity);
        $this->assertArrayHasKey('title_en', $activity->attribute_changes['attributes']);
        $this->assertArrayNotHasKey('embedding_en', $activity->attribute_changes['attributes']);
        $this->assertArrayNotHasKey('embedding_ar', $activity->attribute_changes['attributes']);
    }

    public function test_ai_provider_setting_audit_never_logs_the_api_key(): void
    {
        AiProviderSetting::create([
            'provider' => 'anthropic',
            'model' => 'claude',
            'api_key' => 'sk-super-secret',
            'is_active' => true,
        ]);

        $activity = Activity::where('log_name', 'ai-config')->latest()->first();

        $this->assertNotNull($activity);
        $this->assertArrayHasKey('provider', $activity->attribute_changes['attributes']);
        $this->assertArrayNotHasKey('api_key', $activity->attribute_changes['attributes']);
    }

    public function test_user_audit_logs_role_changes_only(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $baseline = Activity::where('log_name', 'user')->count();

        $user->update(['name' => 'Renamed Only']);
        $this->assertSame($baseline, Activity::where('log_name', 'user')->count());

        $user->update(['role' => 'university_admin']);
        $this->assertSame($baseline + 1, Activity::where('log_name', 'user')->count());
    }

    public function test_guardrail_blocks_are_written_to_the_audit_trail(): void
    {
        SanadChat::fake(['You should take 50mg of sertraline daily.']);

        (new StreamChatResponse('session-uuid', null, 'what helps?', 'en'))
            ->handle(app(ContextInjectionService::class), app(ResponseGuardrailService::class));

        $activity = Activity::where('log_name', 'guardrail')->latest()->first();

        $this->assertNotNull($activity);
        $this->assertSame('Guardrail blocked an AI reply', $activity->description);
        $this->assertContains('medication_dosage', $activity->properties['categories']);
    }

    public function test_audit_log_resource_is_read_only(): void
    {
        $this->assertFalse(ActivityResource::canCreate());
        $this->assertSame(['index'], array_keys(ActivityResource::getPages()));
    }
}
