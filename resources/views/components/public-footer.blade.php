<footer class="border-t border-zinc-200 dark:border-zinc-800">
    <div class="mx-auto max-w-5xl px-6 py-6">
        <div class="flex flex-wrap items-center justify-between gap-4 text-sm text-zinc-500 dark:text-zinc-500">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'Sanad') }}</span>
            <div class="flex items-center gap-4">
                <a href="{{ route('about') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-300">{{ __('legal.about_title') }}</a>
                <a href="{{ route('terms') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-300">{{ __('legal.terms_title') }}</a>
                <a href="{{ route('privacy') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-300">{{ __('legal.privacy_title') }}</a>
            </div>
        </div>
    </div>
</footer>
