<?php

namespace App\Filament\Pages;

use App\Settings\LegalSettings;
use BackedEnum;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ManageLegalSettings extends Page
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $title = 'Legal Pages';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.manage-legal-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = app(LegalSettings::class);

        $this->form->fill([
            'terms_en' => $settings->terms_en,
            'terms_ar' => $settings->terms_ar,
            'privacy_en' => $settings->privacy_en,
            'privacy_ar' => $settings->privacy_ar,
            'about_en' => $settings->about_en,
            'about_ar' => $settings->about_ar,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Legal Pages')
                    ->tabs([
                        Tab::make('Terms & Conditions')
                            ->schema([
                                Textarea::make('terms_en')
                                    ->label('English')
                                    ->rows(15)
                                    ->columnSpanFull(),
                                Textarea::make('terms_ar')
                                    ->label('Arabic')
                                    ->rows(15)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Privacy Policy')
                            ->schema([
                                Textarea::make('privacy_en')
                                    ->label('English')
                                    ->rows(15)
                                    ->columnSpanFull(),
                                Textarea::make('privacy_ar')
                                    ->label('Arabic')
                                    ->rows(15)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('About')
                            ->schema([
                                Textarea::make('about_en')
                                    ->label('English')
                                    ->rows(15)
                                    ->columnSpanFull(),
                                Textarea::make('about_ar')
                                    ->label('Arabic')
                                    ->rows(15)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $settings = app(LegalSettings::class);

        $settings->terms_en = $data['terms_en'] ?? '';
        $settings->terms_ar = $data['terms_ar'] ?? '';
        $settings->privacy_en = $data['privacy_en'] ?? '';
        $settings->privacy_ar = $data['privacy_ar'] ?? '';
        $settings->about_en = $data['about_en'] ?? '';
        $settings->about_ar = $data['about_ar'] ?? '';
        $settings->save();

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
