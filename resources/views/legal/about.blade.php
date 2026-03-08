<x-layouts.public :title="__('legal.about_title')">
    <div class="mx-auto max-w-3xl px-6 py-16">
        <h1 class="mb-8 text-3xl font-bold tracking-tight">{{ __('legal.about_title') }}</h1>

        @php
            $aboutContent = app(\App\Settings\LegalSettings::class)->getAbout(app()->getLocale());
        @endphp

        @if ($aboutContent)
            <div class="prose max-w-none">
                {!! nl2br(e($aboutContent)) !!}
            </div>
        @endif

        {{-- Research Context --}}
        <div class="mt-12 rounded-2xl border border-zinc-200 bg-zinc-50 p-8">
            <h2 class="mb-4 text-2xl font-bold tracking-tight">{{ __('welcome.research_title') }}</h2>
            <p class="leading-relaxed text-zinc-600">
                {{ __('welcome.research_description') }}
            </p>
            <div class="mt-8 flex items-center justify-center gap-10">
                <img src="/AIkfs.png" alt="{{ __('welcome.research_ai_faculty') }}" class="h-20 w-auto object-contain">
                <img src="/kfs.png" alt="{{ __('welcome.research_university') }}" class="h-20 w-auto object-contain">
            </div>
        </div>
    </div>
</x-layouts.public>
