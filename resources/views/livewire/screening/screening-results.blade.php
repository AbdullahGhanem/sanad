<div dir="{{ $this->isRtl ? 'rtl' : 'ltr' }}" class="mx-auto max-w-2xl px-4 py-8">
    {{-- Language Toggle --}}
    <div class="mb-6 flex justify-end gap-2">
        <flux:button size="sm" :variant="$language === 'en' ? 'primary' : 'ghost'" wire:click="switchLanguage('en')">
            English
        </flux:button>
        <flux:button size="sm" :variant="$language === 'ar' ? 'primary' : 'ghost'" wire:click="switchLanguage('ar')">
            العربية
        </flux:button>
    </div>

    {{-- Results Header --}}
    <div class="mb-8 text-center">
        <flux:heading size="xl">{{ __('screening.results_title') }}</flux:heading>
        <flux:text class="mt-2 text-sm text-zinc-500">
            {{ __('screening.results_disclaimer') }}
        </flux:text>
    </div>

    {{-- Score Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
        {{-- PHQ-9 Score --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:text class="text-sm text-zinc-500">{{ __('screening.depression_score') }}</flux:text>
            <div class="mt-2 text-3xl font-bold text-teal-600 dark:text-teal-400">
                {{ $session->phq9_score }} <span class="text-base font-normal text-zinc-400">/ 27</span>
            </div>
            <div class="mt-1 text-sm font-medium {{ $this->severityColor }}">
                {{ __('screening.severity_' . $this->phq9Severity) }}
            </div>
            <flux:text class="mt-2 text-sm">{{ $this->phq9Description }}</flux:text>
        </div>

        {{-- GAD-7 Score --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:text class="text-sm text-zinc-500">{{ __('screening.anxiety_score') }}</flux:text>
            <div class="mt-2 text-3xl font-bold text-teal-600 dark:text-teal-400">
                {{ $session->gad7_score }} <span class="text-base font-normal text-zinc-400">/ 21</span>
            </div>
            <div class="mt-1 text-sm font-medium {{ $this->severityColor }}">
                {{ __('screening.severity_' . $this->gad7Severity) }}
            </div>
            <flux:text class="mt-2 text-sm">{{ $this->gad7Description }}</flux:text>
        </div>
    </div>

    {{-- Overall Assessment --}}
    <div class="mb-8 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="lg" class="mb-3">{{ __('screening.overall_assessment') }}</flux:heading>
        <div class="mb-2 text-lg font-semibold {{ $this->severityColor }}">
            {{ __('screening.severity_' . $session->combined_severity) }}
        </div>
        <flux:text>{{ $this->combinedDescription }}</flux:text>
    </div>

    {{-- Recommendations --}}
    @if ($this->recommendations->isNotEmpty())
        <div class="mb-8">
            <flux:heading size="lg" class="mb-4">{{ __('screening.recommendations') }}</flux:heading>
            <div class="space-y-3">
                @foreach ($this->recommendations as $recommendation)
                    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="font-medium">
                            {{ $language === 'ar' ? $recommendation->title_ar : $recommendation->title_en }}
                        </div>
                        <flux:text class="mt-1 text-sm">
                            {{ $language === 'ar' ? $recommendation->body_ar : $recommendation->body_en }}
                        </flux:text>
                        @if ($recommendation->url)
                            <a href="{{ $recommendation->url }}" target="_blank" class="mt-2 inline-block text-sm text-teal-600 underline">
                                {{ $recommendation->url }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex flex-wrap justify-center gap-3">
        <flux:button variant="primary" :href="route('chat', $session->id)">
            {{ __('screening.chat_title') }}
        </flux:button>
        <flux:button variant="ghost" :href="route('screening.results.pdf', $session->id)">
            {{ __('screening.download_pdf') }}
        </flux:button>
        <flux:button variant="ghost" :href="route('screening')">
            {{ __('screening.retake') }}
        </flux:button>
    </div>
</div>
