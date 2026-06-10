<?php

namespace App\Ai\Tools;

use App\Models\CrisisHelpResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetCrisisResources implements Tool
{
    public function __construct(
        private string $language = 'en',
    ) {}

    /**
     * Get the tool's name as exposed to the model.
     */
    public function name(): string
    {
        return 'get_crisis_resources';
    }

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Retrieve the official, current crisis-support resources (mental-health hotlines, '
            .'websites, and emergency contacts) for Egyptian university students. Call this whenever '
            .'the student expresses suicidal thoughts, self-harm, or acute distress, then share the '
            .'returned details verbatim. Never invent or recall phone numbers or links from memory.';
    }

    /**
     * Get the tool's schema definition.
     *
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()->description(
                'Optional filter for a specific resource type (e.g. "phone", "website", "chat"). '
                .'Omit to retrieve every available resource.'
            ),
        ];
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $resources = CrisisHelpResource::query()
            ->active()
            ->ordered()
            ->when(
                filled($request['type'] ?? null),
                fn ($query) => $query->where('type', $request['type']),
            )
            ->get();

        if ($resources->isEmpty()) {
            return $this->noResourcesFallback();
        }

        return $resources
            ->map(fn (CrisisHelpResource $resource): string => $this->formatResource($resource))
            ->implode("\n");
    }

    /**
     * Format a single resource into a line the model can relay to the student.
     */
    private function formatResource(CrisisHelpResource $resource): string
    {
        $line = "- {$resource->getTitle($this->language)} ({$resource->type}): {$resource->value}";

        if ($detail = $resource->getDetail($this->language)) {
            $line .= " — {$detail}";
        }

        if ($resource->url) {
            $line .= " [{$resource->url}]";
        }

        return $line;
    }

    /**
     * Provide a safe fallback when no resources are configured.
     */
    private function noResourcesFallback(): string
    {
        return $this->language === 'ar'
            ? 'لا توجد موارد مسجلة حاليًا. اطلب من الطالب الاتصال بأقرب جهة طوارئ محلية فورًا إذا كان في خطر.'
            : 'No resources are currently configured. Urge the student to contact their nearest local '
                .'emergency service immediately if they are in danger.';
    }
}
