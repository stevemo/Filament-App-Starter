<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Facades\Storage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageGeneralSetting extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'General';

    protected static string $settings = GeneralSettings::class;

    protected static ?int $navigationSort = 99;

    public function getRedirectUrl(): ?string
    {
        return static::getUrl();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Site')
                            ->icon('fluentui-web-asset-24-o')
                            ->columns(4)
                            ->schema([
                                Forms\Components\TextInput::make('brand_name')
                                    ->columnSpan(2)
                                    ->required(),

                                Forms\Components\FileUpload::make('brand_logo')
                                    ->directory('settings')
                                    ->moveFiles()
                                    ->columnStart(1)
                                    ->columnSpan(2)
                                    ->deleteUploadedFileUsing(fn (Get $get) => Storage::delete(app(GeneralSettings::class)->brand_logo)),

                                Forms\Components\TextInput::make('brand_logoHeight')
                                    ->columnSpan(1)
                                    ->formatStateUsing(fn (string $state): string => str($state)->remove('rem'))
                                    ->dehydrateStateUsing(fn (string $state): string => $state.'rem'),

                                Forms\Components\FileUpload::make('site_favicon')
                                    ->directory('settings')
                                    ->moveFiles()
                                    ->columnStart(1)
                                    ->deleteUploadedFileUsing(fn (Get $get) => Storage::delete(app(GeneralSettings::class)->site_favicon)),
                            ]),

                        Tabs\Tab::make('Colors')
                            ->icon('fluentui-color-24-o')
                            ->schema([
                                Forms\Components\ColorPicker::make('site_theme.primary')->rgb(),
                                Forms\Components\ColorPicker::make('site_theme.secondary')->rgb(),
                                Forms\Components\ColorPicker::make('site_theme.success')->rgb(),
                                Forms\Components\ColorPicker::make('site_theme.danger')->rgb(),
                                Forms\Components\ColorPicker::make('site_theme.info')->rgb(),
                                Forms\Components\ColorPicker::make('site_theme.warning')->rgb(),
                                Forms\Components\ColorPicker::make('site_theme.gray')->rgb(),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
