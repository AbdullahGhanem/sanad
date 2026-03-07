<div dir="{{ $this->isRtl ? 'rtl' : 'ltr' }}" class="mx-auto max-w-2xl px-4 py-8">
    @if ($isCompleted)
        {{-- Results Summary --}}
        <div class="rounded-xl border border-green-200 bg-green-50 p-8 text-center dark:border-green-800 dark:bg-green-950">
            <div class="mb-4 text-4xl">✓</div>
            <flux:heading size="lg">
                {{ __('screening.completed_title') }}
            </flux:heading>
            <flux:text class="mt-2">
                {{ __('screening.completed_message') }}
            </flux:text>
            <div class="mt-6">
                <flux:button variant="primary" :href="route('screening.results', $screeningSessionId)">
                    {{ __('screening.view_results') }}
                </flux:button>
            </div>
        </div>
    @elseif ($phase === 'free_text')
        {{-- Progress Bar (100%) --}}
        <div class="mb-8">
            <div class="h-2 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                <div class="h-2 w-full rounded-full bg-teal-500 transition-all duration-300"></div>
            </div>
        </div>

        {{-- Free Text Input --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">
                {{ __('screening.free_text_title') }}
            </flux:heading>

            <flux:text class="mb-4 text-sm text-zinc-500">
                {{ __('screening.free_text_instruction') }}
            </flux:text>

            <div class="mb-2">
                <textarea
                    wire:model.live.debounce.300ms="freeText"
                    maxlength="500"
                    rows="5"
                    class="w-full rounded-lg border border-zinc-200 bg-white p-4 text-sm focus:border-teal-500 focus:ring-teal-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    placeholder="{{ __('screening.free_text_placeholder') }}"
                ></textarea>
                <div class="flex justify-end text-xs text-zinc-400">
                    <span>{{ 500 - strlen($freeText) }} {{ __('screening.free_text_chars') }}</span>
                </div>
            </div>

            <div class="mt-2 flex items-start gap-2 rounded-lg bg-zinc-50 p-3 text-xs text-zinc-500 dark:bg-zinc-800">
                <span class="mt-0.5">🔒</span>
                <span>{{ __('screening.free_text_privacy') }}</span>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="mt-6 flex justify-between">
            <flux:button variant="ghost" wire:click="previous">
                {{ __('screening.previous') }}
            </flux:button>

            <div class="flex gap-2">
                <flux:button variant="ghost" wire:click="skipFreeText">
                    {{ __('screening.free_text_skip') }}
                </flux:button>
                @if (strlen($freeText) > 0)
                    <flux:button variant="primary" wire:click="submitFreeText">
                        {{ __('screening.free_text_submit') }}
                    </flux:button>
                @endif
            </div>
        </div>
    @else
        {{-- Progress Bar --}}
        <div class="mb-8">
            <div class="mb-2 flex justify-between text-sm">
                <flux:text>
                    {{ __('screening.question') }} {{ $currentStep + 1 }} / {{ $this->totalSteps }}
                </flux:text>
                <flux:text>
                    {{ $phase === 'phq9' ? __('screening.depression_phq9') : __('screening.anxiety_gad7') }}
                </flux:text>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                <div
                    class="h-2 rounded-full bg-teal-500 transition-all duration-300"
                    style="width: {{ $this->progressPercentage }}%"
                ></div>
            </div>
        </div>

        {{-- Question Card --}}
        @if ($this->currentQuestion)
            <div class="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900" wire:key="question-{{ $currentStep }}">
                <flux:heading size="lg" class="mb-6">
                    {{ $language === 'ar' ? $this->currentQuestion->text_ar : $this->currentQuestion->text_en }}
                </flux:heading>

                <flux:text class="mb-6 text-sm text-zinc-500">
                    {{ __('screening.instruction') }}
                </flux:text>

                <div class="space-y-3">
                    @foreach ($this->currentQuestion->options as $option)
                        <button
                            wire:click="selectOption({{ $option->value }})"
                            wire:key="option-{{ $currentStep }}-{{ $option->id }}"
                            class="w-full rounded-lg border px-4 py-3 text-start transition-colors
                                {{ $answers[$currentStep] === $option->value
                                    ? 'border-teal-500 bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300'
                                    : 'border-zinc-200 hover:border-teal-300 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-teal-700 dark:hover:bg-zinc-800'
                                }}"
                        >
                            {{ $language === 'ar' ? $option->label_ar : $option->label_en }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Navigation --}}
            <div class="mt-6 flex justify-between">
                <div>
                    @if ($currentStep > 0)
                        <flux:button variant="ghost" wire:click="previous">
                            {{ __('screening.previous') }}
                        </flux:button>
                    @endif
                </div>

                <div>
                    @if ($answers[$currentStep] !== null)
                        <flux:button variant="primary" wire:click="next">
                            {{ $currentStep + 1 >= $this->totalSteps ? __('screening.complete') : __('screening.next') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        @endif

        {{-- Crisis Overlay --}}
        @if ($showCrisisOverlay)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
                <div class="max-w-lg rounded-2xl border-2 border-red-500 bg-white p-8 shadow-2xl dark:bg-zinc-900">
                    <div class="mb-4 text-center text-3xl text-red-500">⚠️</div>
                    <flux:heading size="lg" class="mb-4 text-center text-red-600 dark:text-red-400">
                        {{ __('screening.crisis_title') }}
                    </flux:heading>

                    <flux:text class="mb-6">
                        {{ __('screening.crisis_message') }}
                    </flux:text>

                    <div class="mb-6 space-y-3 rounded-lg bg-red-50 p-4 dark:bg-red-950">
                        @foreach ($this->crisisHelpResources as $resource)
                            <div class="flex items-center gap-2">
                                <span>@switch($resource->icon)
                                    @case('phone') 📞 @break
                                    @case('globe') 🌐 @break
                                    @case('hospital') 🏥 @break
                                    @case('chat') 💬 @break
                                    @case('email') 📧 @break
                                    @default ℹ️
                                @endswitch</span>
                                <div>
                                    <div class="font-semibold">{{ $resource->getTitle($language) }}</div>
                                    @if ($resource->url)
                                        <a href="{{ $resource->url }}" target="_blank" class="text-sm text-teal-600 underline">{{ $resource->value }}</a>
                                    @elseif ($resource->value)
                                        <div class="text-sm">{{ $resource->value }}</div>
                                    @endif
                                    @if ($resource->getDetail($language))
                                        <div class="text-sm">{{ $resource->getDetail($language) }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <flux:button variant="primary" class="w-full" wire:click="acknowledgeCrisis">
                        {{ __('screening.crisis_acknowledge') }}
                    </flux:button>
                </div>
            </div>
        @endif
    @endif

    {{-- Loading State --}}
    <div wire:loading class="fixed inset-0 z-40 flex items-center justify-center bg-black/30">
        <div class="rounded-lg bg-white p-4 shadow-lg dark:bg-zinc-800">
            <flux:text>{{ __('screening.loading') }}</flux:text>
        </div>
    </div>
</div>
