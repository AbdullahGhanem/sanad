<x-layouts.public :title="__('legal.privacy_title')">
    <div class="mx-auto max-w-3xl px-6 py-16">
        <h1 class="mb-8 text-3xl font-bold tracking-tight">{{ __('legal.privacy_title') }}</h1>
        <div class="prose max-w-none dark:prose-invert">
            {!! nl2br(e(app(\App\Settings\LegalSettings::class)->getPrivacy(app()->getLocale()))) !!}
        </div>
    </div>
</x-layouts.public>
