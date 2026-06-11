@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\CounselorNote> $notes */
@endphp

<div class="space-y-3">
    @forelse ($notes as $note)
        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700 dark:bg-gray-900">
            <div class="mb-1 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span class="font-medium text-gray-700 dark:text-gray-300">
                    {{ $note->author?->name ?? __('admin.system_author') }}
                </span>
                <time datetime="{{ $note->created_at->toIso8601String() }}">
                    {{ $note->created_at->diffForHumans() }}
                </time>
            </div>
            <p class="whitespace-pre-wrap text-sm text-gray-800 dark:text-gray-200">{{ $note->body }}</p>
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.no_notes_yet') }}</p>
    @endforelse
</div>
