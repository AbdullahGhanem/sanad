<?php

use Livewire\Component;

new class extends Component
{
    public function switchLanguage(string $locale): void
    {
        if (in_array($locale, ['ar', 'en'])) {
            session()->put('locale', $locale);
            $this->redirect(request()->header('Referer', '/'), navigate: true);
        }
    }
};
?>

<flux:dropdown position="bottom" align="end">
    <flux:tooltip :content="__('Language')" position="bottom">
        <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="language" :label="__('Language')" />
    </flux:tooltip>

    <flux:menu>
        <flux:menu.item wire:click="switchLanguage('ar')" icon="{{ app()->getLocale() === 'ar' ? 'check' : '' }}">
            العربية
        </flux:menu.item>
        <flux:menu.item wire:click="switchLanguage('en')" icon="{{ app()->getLocale() === 'en' ? 'check' : '' }}">
            English
        </flux:menu.item>
    </flux:menu>
</flux:dropdown>
