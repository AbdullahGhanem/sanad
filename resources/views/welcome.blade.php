<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('welcome.title') }}</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|cairo:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100" style="font-family: '{{ app()->getLocale() === 'ar' ? 'Cairo' : 'Instrument Sans' }}', sans-serif;">
        <x-public-header />

        {{-- Hero Section --}}
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-teal-50 via-white to-cyan-50 dark:from-teal-950/20 dark:via-zinc-950 dark:to-cyan-950/20"></div>
            <div class="relative mx-auto max-w-5xl px-6 py-24 text-center sm:py-32">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-teal-200 bg-teal-50 px-4 py-1.5 text-sm font-medium text-teal-700 dark:border-teal-800 dark:bg-teal-950/50 dark:text-teal-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ __('welcome.badge') }}
                </div>
                <h1 class="mx-auto max-w-3xl text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                    {{ __('welcome.hero_title') }} <span class="text-teal-600 dark:text-teal-400">{{ __('welcome.hero_highlight') }}</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-zinc-600 dark:text-zinc-400">
                    {{ __('welcome.hero_description') }}
                </p>
                <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <a href="{{ route('screening') }}" class="inline-flex items-center gap-2 rounded-xl bg-teal-600 px-8 py-3.5 text-base font-semibold text-white shadow-lg shadow-teal-600/25 transition hover:bg-teal-700 hover:shadow-teal-600/40">
                        {{ __('welcome.start_screening') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-500">{{ __('welcome.no_account_required') }}</p>
            </div>
        </section>

        {{-- Features Section --}}
        <section class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900/50">
            <div class="mx-auto max-w-5xl px-6 py-20">
                <div class="mb-12 text-center">
                    <h2 class="text-3xl font-bold tracking-tight">{{ __('welcome.features_title') }}</h2>
                    <p class="mt-3 text-zinc-600 dark:text-zinc-400">{{ __('welcome.features_subtitle') }}</p>
                </div>
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    {{-- Feature 1 --}}
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-teal-100 text-teal-600 dark:bg-teal-900/50 dark:text-teal-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">{{ __('welcome.feature_anonymous_title') }}</h3>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('welcome.feature_anonymous_desc') }}</p>
                    </div>
                    {{-- Feature 2 --}}
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600 dark:bg-cyan-900/50 dark:text-cyan-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">{{ __('welcome.feature_evidence_title') }}</h3>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('welcome.feature_evidence_desc') }}</p>
                    </div>
                    {{-- Feature 3 --}}
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-900/50 dark:text-violet-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13 7H7v6h6V7z" /><path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">{{ __('welcome.feature_ai_title') }}</h3>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('welcome.feature_ai_desc') }}</p>
                    </div>
                    {{-- Feature 4 --}}
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7 2a1 1 0 011 1v1h3a1 1 0 110 2H9.578a18.87 18.87 0 01-1.724 4.78c.29.354.596.696.914 1.026a1 1 0 11-1.44 1.389c-.188-.196-.373-.396-.554-.6a19.098 19.098 0 01-3.107 3.567 1 1 0 01-1.334-1.49 17.087 17.087 0 003.13-3.733 18.992 18.992 0 01-1.487-2.494 1 1 0 111.79-.89c.234.47.489.928.764 1.372.417-.934.752-1.913.997-2.927H3a1 1 0 110-2h3V3a1 1 0 011-1zm6 6a1 1 0 01.894.553l2.991 5.982a.869.869 0 01.02.037l.99 1.98a1 1 0 11-1.79.895L15.383 16h-4.764l-.724 1.447a1 1 0 11-1.788-.894l.99-1.98.019-.038 2.99-5.982A1 1 0 0113 8zm-1.382 6h2.764L13 11.236 11.618 14z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">{{ __('welcome.feature_bilingual_title') }}</h3>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('welcome.feature_bilingual_desc') }}</p>
                    </div>
                    {{-- Feature 5 --}}
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-900/50 dark:text-rose-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">{{ __('welcome.feature_recommendations_title') }}</h3>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('welcome.feature_recommendations_desc') }}</p>
                    </div>
                    {{-- Feature 6 --}}
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">{{ __('welcome.feature_pdf_title') }}</h3>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('welcome.feature_pdf_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- How It Works Section --}}
        <section class="border-t border-zinc-200 dark:border-zinc-800">
            <div class="mx-auto max-w-5xl px-6 py-20">
                <div class="mb-12 text-center">
                    <h2 class="text-3xl font-bold tracking-tight">{{ __('welcome.how_title') }}</h2>
                    <p class="mt-3 text-zinc-600 dark:text-zinc-400">{{ __('welcome.how_subtitle') }}</p>
                </div>
                <div class="grid gap-8 sm:grid-cols-3">
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-teal-600 text-lg font-bold text-white">{{ app()->getLocale() === 'ar' ? '١' : '1' }}</div>
                        <h3 class="mb-2 font-semibold">{{ __('welcome.step1_title') }}</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('welcome.step1_desc') }}</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-teal-600 text-lg font-bold text-white">{{ app()->getLocale() === 'ar' ? '٢' : '2' }}</div>
                        <h3 class="mb-2 font-semibold">{{ __('welcome.step2_title') }}</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('welcome.step2_desc') }}</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-teal-600 text-lg font-bold text-white">{{ app()->getLocale() === 'ar' ? '٣' : '3' }}</div>
                        <h3 class="mb-2 font-semibold">{{ __('welcome.step3_title') }}</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('welcome.step3_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="border-t border-zinc-200 bg-teal-600 dark:border-zinc-800">
            <div class="mx-auto max-w-5xl px-6 py-16 text-center">
                <h2 class="text-3xl font-bold tracking-tight text-white">{{ __('welcome.cta_title') }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-teal-100">{{ __('welcome.cta_subtitle') }}</p>
                <div class="mt-8">
                    <a href="{{ route('screening') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-3.5 text-base font-semibold text-teal-700 shadow-lg transition hover:bg-teal-50">
                        {{ __('welcome.start_screening') }}
                    </a>
                </div>
            </div>
        </section>

        {{-- Disclaimer --}}
        <section class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900/50">
            <div class="mx-auto max-w-5xl px-6 py-10 text-center">
                <p class="text-sm leading-relaxed text-zinc-500 dark:text-zinc-500">
                    {!! __('welcome.disclaimer', ['hotline' => '<strong class="text-zinc-700 dark:text-zinc-300">' . \App\Models\CrisisHelpResource::hotlineNumber() . '</strong>']) !!}
                </p>
            </div>
        </section>

        <x-public-footer />
    </body>
</html>
