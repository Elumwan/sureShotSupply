<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
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
            'seo_site_name' => SiteSetting::get('seo_site_name', 'SureShotSupply'),
            'seo_default_title' => SiteSetting::get('seo_default_title', 'SureShotSupply — Cameras & Accessories'),
            'seo_default_description' => SiteSetting::get(
                'seo_default_description',
                'Hand-picked second-hand cameras and premium accessories. Shop SureShotSupply.'
            ),
            'seo_og_image' => SiteSetting::get('seo_og_image'),
            'seo_twitter_handle' => SiteSetting::get('seo_twitter_handle'),
            'shipping_australia' => (int) SiteSetting::get('shipping_australia', 1000),
            'shipping_new_zealand' => (int) SiteSetting::get('shipping_new_zealand', 1500),
            'shipping_uk_us_canada' => (int) SiteSetting::get('shipping_uk_us_canada', 2500),
            'shipping_europe_asia_pacific' => (int) SiteSetting::get('shipping_europe_asia_pacific', 3000),
            'shipping_rest_of_world' => (int) SiteSetting::get('shipping_rest_of_world', 3500),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Hero')
                    ->schema([
                        FileUpload::make('hero_image')
                            ->label('Hero background image')
                            ->disk('public')
                            ->visibility('public')
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
                    ]),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_site_name')
                            ->label('Site name')
                            ->required(),
                        TextInput::make('seo_default_title')
                            ->label('Default title')
                            ->required(),
                        Textarea::make('seo_default_description')
                            ->label('Default description')
                            ->rows(3)
                            ->required(),
                        TextInput::make('seo_og_image')
                            ->label('Open Graph image URL')
                            ->url(),
                        TextInput::make('seo_twitter_handle')
                            ->label('Twitter handle'),
                    ])
                    ->columns(2),
                Section::make('Shipping Rates (AUD)')
                    ->description('Values are stored in cents. Example: 1000 = $10.00.')
                    ->schema([
                        TextInput::make('shipping_australia')
                            ->label('Australia')
                            ->required()
                            ->integer()
                            ->numeric(),
                        TextInput::make('shipping_new_zealand')
                            ->label('New Zealand')
                            ->required()
                            ->integer()
                            ->numeric(),
                        TextInput::make('shipping_uk_us_canada')
                            ->label('UK, US & Canada')
                            ->required()
                            ->integer()
                            ->numeric(),
                        TextInput::make('shipping_europe_asia_pacific')
                            ->label('Europe & Asia Pacific')
                            ->required()
                            ->integer()
                            ->numeric(),
                        TextInput::make('shipping_rest_of_world')
                            ->label('Rest of World')
                            ->required()
                            ->integer()
                            ->numeric(),
                    ])
                    ->columns(2),
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
        SiteSetting::set('seo_site_name', $state['seo_site_name'] ?? 'SureShotSupply');
        SiteSetting::set('seo_default_title', $state['seo_default_title'] ?? 'SureShotSupply — Cameras & Accessories');
        SiteSetting::set(
            'seo_default_description',
            $state['seo_default_description'] ?? 'Hand-picked second-hand cameras and premium accessories. Shop SureShotSupply.'
        );
        SiteSetting::set('seo_og_image', $state['seo_og_image'] ?: null);
        SiteSetting::set('seo_twitter_handle', $state['seo_twitter_handle'] ?: null);
        SiteSetting::set('shipping_australia', (string) ($state['shipping_australia'] ?? 1000));
        SiteSetting::set('shipping_new_zealand', (string) ($state['shipping_new_zealand'] ?? 1500));
        SiteSetting::set('shipping_uk_us_canada', (string) ($state['shipping_uk_us_canada'] ?? 2500));
        SiteSetting::set(
            'shipping_europe_asia_pacific',
            (string) ($state['shipping_europe_asia_pacific'] ?? 3000)
        );
        SiteSetting::set('shipping_rest_of_world', (string) ($state['shipping_rest_of_world'] ?? 3500));

        Notification::make()
            ->title('Site settings saved')
            ->success()
            ->send();
    }
}
