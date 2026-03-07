<nav class="sticky top-0 z-50 border-b border-zinc-200 bg-white/80 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/80">
    <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2" wire:navigate>
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-600 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
            </div>
            <span class="text-lg font-semibold">{{ config('app.name', 'Sanad') }}</span>
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('language.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="rounded-lg border border-zinc-300 px-3 py-1.5 text-sm font-medium text-zinc-600 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800">
                {{ app()->getLocale() === 'ar' ? 'EN' : 'عربي' }}
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-teal-700" wire:navigate>{{ __('welcome.dashboard') }}</a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white" wire:navigate>{{ __('welcome.login') }}</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-teal-700" wire:navigate>{{ __('welcome.register') }}</a>
                @endif
            @endauth
        </div>
    </div>
</nav>
