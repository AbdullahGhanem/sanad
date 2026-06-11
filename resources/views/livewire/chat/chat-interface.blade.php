<div
    dir="{{ $this->isRtl ? 'rtl' : 'ltr' }}"
    class="mx-auto flex h-[calc(100vh-180px)] max-w-2xl flex-col px-4 py-4"
    x-data="{
        inputText: '',
        pendingMessage: '',
        isSending: false,
        isStreaming: false,
        streamingText: '',
        isRecording: false,
        isTranscribing: false,
        mediaRecorder: null,
        audioChunks: [],
        channel: '{{ $this->streamChannel }}',
        init() {
            this.$nextTick(() => this.scrollToBottom());

            $wire.on('transcription-ready', (e) => {
                this.isTranscribing = false;
                this.inputText = (e?.text) ?? this.inputText;
                this.$nextTick(() => this.$refs.messageInput?.focus());
            });
            $wire.on('transcription-failed', () => {
                this.isTranscribing = false;
            });

            if (window.Echo) {
                window.Echo.channel(this.channel)
                    .listen('.text_delta', (e) => {
                        this.isSending = false;
                        this.isStreaming = true;
                        this.streamingText += e.delta ?? '';
                        this.$nextTick(() => this.scrollToBottom());
                    })
                    .listen('.stream_end', () => {
                        $wire.finishStreaming().then(() => {
                            this.streamingText = '';
                            this.isStreaming = false;
                            this.isSending = false;
                            this.$nextTick(() => this.scrollToBottom());
                        });
                    });
            }
        },
        sendOptimistic() {
            const text = this.inputText.trim();
            if (!text || this.isSending || this.isStreaming) return;

            this.pendingMessage = text;
            this.isSending = true;
            this.streamingText = '';
            this.inputText = '';

            this.$nextTick(() => this.scrollToBottom());

            $wire.set('message', text).then(() => {
                $wire.sendMessage().then(() => {
                    this.pendingMessage = '';
                    this.$nextTick(() => {
                        this.scrollToBottom();
                        this.$refs.messageInput.focus();
                    });
                });
            });
        },
        async toggleRecording() {
            if (this.isRecording) {
                this.mediaRecorder?.stop();
                return;
            }

            if (!navigator.mediaDevices?.getUserMedia || typeof MediaRecorder === 'undefined') {
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.audioChunks = [];
                this.mediaRecorder = new MediaRecorder(stream);

                this.mediaRecorder.addEventListener('dataavailable', (e) => {
                    if (e.data.size > 0) this.audioChunks.push(e.data);
                });

                this.mediaRecorder.addEventListener('stop', () => {
                    stream.getTracks().forEach((t) => t.stop());
                    this.isRecording = false;

                    const blob = new Blob(this.audioChunks, { type: 'audio/webm' });
                    if (blob.size === 0) return;

                    const file = new File([blob], 'voice.webm', { type: 'audio/webm' });
                    this.isTranscribing = true;
                    $wire.upload('audioFile', file, () => {}, () => { this.isTranscribing = false; });
                });

                this.mediaRecorder.start();
                this.isRecording = true;
            } catch (e) {
                this.isRecording = false;
            }
        },
        scrollToBottom() {
            const el = document.getElementById('chat-messages');
            if (el) el.scrollTop = el.scrollHeight;
        }
    }"
>
    {{-- Header --}}
    <div class="mb-4">
        <flux:heading size="lg">{{ __('screening.chat_title') }}</flux:heading>
    </div>

    {{-- Messages Area --}}
    <div class="flex-1 space-y-4 overflow-y-auto rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900" id="chat-messages">
        @forelse ($this->chatMessages as $msg)
            <div class="flex {{ $msg->role === 'user' ? ($this->isRtl ? 'justify-start' : 'justify-end') : ($this->isRtl ? 'justify-end' : 'justify-start') }}" wire:key="msg-{{ $msg->id }}">
                <div class="max-w-[80%] rounded-2xl px-4 py-3 {{ $msg->role === 'user'
                    ? 'bg-teal-500 text-white'
                    : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200'
                }}">
                    <div class="whitespace-pre-wrap text-sm">{{ $msg->content }}</div>
                    @if ($msg->detected_crisis)
                        <div class="mt-1 text-xs {{ $msg->role === 'user' ? 'text-teal-100' : 'text-red-500' }}">
                            ⚠️
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <template x-if="!pendingMessage">
                <div class="flex h-full items-center justify-center text-center">
                    <div>
                        <div class="mb-2 text-4xl">💬</div>
                        <flux:text class="text-zinc-400">{{ __('screening.chat_start') }}</flux:text>
                    </div>
                </div>
            </template>
        @endforelse

        {{-- Optimistic user message (shown instantly before server responds) --}}
        <template x-if="pendingMessage">
            <div class="flex {{ $this->isRtl ? 'justify-start' : 'justify-end' }}">
                <div class="max-w-[80%] rounded-2xl bg-teal-500 px-4 py-3 text-white">
                    <div class="whitespace-pre-wrap text-sm" x-text="pendingMessage"></div>
                </div>
            </div>
        </template>

        {{-- Live streaming assistant message (tokens arrive over websockets) --}}
        <template x-if="isStreaming && streamingText">
            <div class="flex {{ $this->isRtl ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] rounded-2xl bg-zinc-100 px-4 py-3 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                    <div class="whitespace-pre-wrap text-sm" x-text="streamingText"></div>
                </div>
            </div>
        </template>

        {{-- Typing indicator (until the first token arrives) --}}
        <template x-if="isSending && !streamingText">
            <div class="flex {{ $this->isRtl ? 'justify-end' : 'justify-start' }}">
                <div class="rounded-2xl bg-zinc-100 px-4 py-3 dark:bg-zinc-800">
                    <div class="flex gap-1">
                        <span class="h-2 w-2 animate-bounce rounded-full bg-zinc-400" style="animation-delay: 0ms"></span>
                        <span class="h-2 w-2 animate-bounce rounded-full bg-zinc-400" style="animation-delay: 150ms"></span>
                        <span class="h-2 w-2 animate-bounce rounded-full bg-zinc-400" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Disclaimer --}}
    <div class="mt-2 text-center text-xs text-zinc-400">
        {{ __('screening.chat_disclaimer', ['hotline' => \App\Models\CrisisHelpResource::hotlineNumber()]) }}
    </div>

    {{-- Input Area --}}
    <form @submit.prevent="sendOptimistic" class="mt-3 flex gap-2">
        {{-- Voice input (record → transcribe → review) --}}
        <button
            type="button"
            @click="toggleRecording"
            :disabled="isSending || isStreaming || isTranscribing"
            :class="isRecording ? 'border-red-500 bg-red-50 text-red-600 dark:bg-red-950' : 'border-zinc-200 bg-white text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800'"
            class="flex w-12 items-center justify-center rounded-xl border transition disabled:opacity-50"
            :aria-label="isRecording ? '{{ __('screening.chat_stop_recording') }}' : '{{ __('screening.chat_record') }}'"
            title="{{ __('screening.chat_record') }}"
        >
            <svg x-show="isTranscribing" x-cloak class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span x-show="isRecording && !isTranscribing" class="h-3 w-3 animate-pulse rounded-sm bg-red-600"></span>
            <svg x-show="!isRecording && !isTranscribing" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3m0-3a3 3 0 01-3-3V6a3 3 0 116 0v6a3 3 0 01-3 3z" />
            </svg>
        </button>
        <input
            type="text"
            x-ref="messageInput"
            x-model="inputText"
            placeholder="{{ __('screening.chat_placeholder') }}"
            maxlength="1000"
            class="flex-1 rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm focus:border-teal-500 focus:ring-teal-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
            autocomplete="off"
            :disabled="isSending || isStreaming"
        />
        <flux:button type="submit" variant="primary" ::disabled="isSending || isStreaming">
            <span x-show="!isSending && !isStreaming">{{ __('screening.chat_send') }}</span>
            <span x-show="isSending || isStreaming" x-cloak>
                <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </span>
        </flux:button>
    </form>

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
</div>
