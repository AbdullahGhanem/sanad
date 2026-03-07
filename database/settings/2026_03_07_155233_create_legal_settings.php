<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('legal.terms_en', '');
        $this->migrator->add('legal.terms_ar', '');
        $this->migrator->add('legal.privacy_en', '');
        $this->migrator->add('legal.privacy_ar', '');
        $this->migrator->add('legal.about_en', '');
        $this->migrator->add('legal.about_ar', '');
    }
};
