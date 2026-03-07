<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LegalSettings extends Settings
{
    public string $terms_en;

    public string $terms_ar;

    public string $privacy_en;

    public string $privacy_ar;

    public string $about_en;

    public string $about_ar;

    public static function group(): string
    {
        return 'legal';
    }

    public function getTerms(string $locale = 'ar'): string
    {
        return $locale === 'ar' ? $this->terms_ar : $this->terms_en;
    }

    public function getPrivacy(string $locale = 'ar'): string
    {
        return $locale === 'ar' ? $this->privacy_ar : $this->privacy_en;
    }

    public function getAbout(string $locale = 'ar'): string
    {
        return $locale === 'ar' ? $this->about_ar : $this->about_en;
    }
}
