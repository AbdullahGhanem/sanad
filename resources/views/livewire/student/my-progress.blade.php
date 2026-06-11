<div dir="{{ $this->isRtl ? 'rtl' : 'ltr' }}" class="mx-auto max-w-3xl px-4 py-8">
    <div class="mb-6 flex items-center justify-between gap-4">
        <flux:heading size="xl">{{ __('progress.title') }}</flux:heading>
        <flux:button :href="route('screening')" variant="primary" size="sm">
            {{ __('progress.new_screening') }}
        </flux:button>
    </div>

    {{-- Request a counselor (student-initiated referral) --}}
    @if ($this->hasPendingRequest)
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-teal-200 bg-teal-50 p-4 dark:border-teal-800 dark:bg-teal-950">
            <span class="text-2xl">✅</span>
            <flux:text class="text-teal-800 dark:text-teal-200">{{ __('progress.request_pending') }}</flux:text>
        </div>
    @else
        <div class="mb-6 flex flex-col items-start justify-between gap-3 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 sm:flex-row sm:items-center">
            <div>
                <div class="font-medium">{{ __('progress.request_title') }}</div>
                <flux:text class="text-sm text-zinc-500">{{ __('progress.request_body') }}</flux:text>
            </div>
            <flux:button
                variant="primary"
                wire:click="requestCounselor"
                wire:confirm="{{ __('progress.request_confirm') }}"
                wire:loading.attr="disabled"
            >
                {{ __('progress.request_counselor') }}
            </flux:button>
        </div>
    @endif

    @if ($this->sessions->isEmpty())
        <div class="rounded-xl border border-zinc-200 bg-white p-10 text-center dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-3 text-4xl">📈</div>
            <flux:heading size="lg">{{ __('progress.empty_title') }}</flux:heading>
            <flux:text class="mb-6 mt-2 text-zinc-500">{{ __('progress.empty_body') }}</flux:text>
            <flux:button :href="route('screening')" variant="primary">{{ __('progress.take_first') }}</flux:button>
        </div>
    @else
        @php($latest = $this->sessions->last())

        {{-- Summary --}}
        <div class="mb-6 grid grid-cols-3 gap-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-4 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <div class="text-xs text-zinc-400">{{ __('progress.screenings') }}</div>
                <div class="mt-1 text-2xl font-semibold">{{ $this->sessions->count() }}</div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <div class="text-xs text-zinc-400">{{ __('progress.latest_severity') }}</div>
                <div class="mt-1 text-lg font-semibold {{ $this->severityColor($latest->combined_severity) }}">
                    {{ __('screening.severity_'.$latest->combined_severity) }}
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <div class="text-xs text-zinc-400">{{ __('progress.trend') }}</div>
                <div class="mt-1 text-lg font-semibold">
                    @if ($this->trend === 'improving')
                        <span class="text-green-600 dark:text-green-400">↓ {{ __('progress.improving') }}</span>
                    @elseif ($this->trend === 'worsening')
                        <span class="text-red-600 dark:text-red-400">↑ {{ __('progress.worsening') }}</span>
                    @elseif ($this->trend === 'stable')
                        <span class="text-zinc-500">→ {{ __('progress.stable') }}</span>
                    @else
                        <span class="text-zinc-400">—</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- PHQ-9 trend sparkline --}}
        @if ($this->phq9Sparkline)
            <div class="mb-6 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="mb-2 text-xs text-zinc-400">{{ __('progress.phq9_trend') }}</div>
                <svg viewBox="0 0 100 32" preserveAspectRatio="none" class="h-16 w-full text-teal-500">
                    <polyline points="{{ $this->phq9Sparkline }}" fill="none" stroke="currentColor" stroke-width="1.5" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        @endif

        {{-- History --}}
        <div class="divide-y divide-zinc-100 overflow-hidden rounded-xl border border-zinc-200 bg-white dark:divide-zinc-800 dark:border-zinc-700 dark:bg-zinc-900">
            @foreach ($this->sessions->reverse() as $session)
                <div class="flex items-center justify-between gap-4 p-4">
                    <div>
                        <div class="text-sm font-medium">{{ $session->completed_at->translatedFormat('d MMM Y') }}</div>
                        <div class="text-xs text-zinc-400">PHQ-9: {{ $session->phq9_score }} · GAD-7: {{ $session->gad7_score }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold {{ $this->severityColor($session->combined_severity) }}">
                            {{ __('screening.severity_'.$session->combined_severity) }}
                        </span>
                        <flux:button :href="route('screening.results', $session->id)" size="xs" variant="ghost">
                            {{ __('progress.view') }}
                        </flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
