<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'hero_image' => SiteSetting::get('hero_image'),
            'hero_title' => SiteSetting::get('hero_title', 'Gear for those who shoot first.'),
            'hero_subtitle' => SiteSetting::get(
                'hero_subtitle',
                'Handpicked cameras and accessories for photographers who care about what they carry.'
            ),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('hero_image')
                    ->label('Hero background image')
                    ->disk('public')
                    ->directory('site')
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->maxSize(8192)
                    ->image(),
                Textarea::make('hero_title')
                    ->label('Hero title')
                    ->default('Gear for those who shoot first.')
                    ->rows(2)
                    ->required(),
                Textarea::make('hero_subtitle')
                    ->label('Hero subtitle')
                    ->rows(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        SiteSetting::set('hero_image', $state['hero_image'] ?? null);
        SiteSetting::set('hero_title', $state['hero_title'] ?? 'Gear for those who shoot first.');
        SiteSetting::set(
            'hero_subtitle',
            $state['hero_subtitle'] ?? 'Handpicked cameras and accessories for photographers who care about what they carry.'
        );

        Notification::make()
            ->title('Site settings saved')
            ->success()
            ->send();
    }
}
