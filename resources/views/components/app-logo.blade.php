@props([
    'sidebar' => false,
])

@if($sidebar)
    <a {{ $attributes }} class="flex items-center gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-600 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
            </svg>
        </div>
        <span class="text-lg font-semibold text-zinc-900 dark:text-white">{{ config('app.name', 'Sanad') }}</span>
    </a>
@else
    <a {{ $attributes }} class="flex items-center gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-600 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
            </svg>
        </div>
        <span class="text-lg font-semibold text-zinc-900 dark:text-white">{{ config('app.name', 'Sanad') }}</span>
    </a>
@endif
