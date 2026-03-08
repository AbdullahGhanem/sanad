@props(['title' => config('app.name')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100" style="font-family: '{{ app()->getLocale() === 'ar' ? 'Cairo' : 'Instrument Sans' }}', sans-serif;">
        <x-public-header />

        <main class="min-h-[calc(100vh-theme(spacing.32))]">
            {{ $slot }}
        </main>

        <x-public-footer />

        @fluxScripts
    </body>
</html>
