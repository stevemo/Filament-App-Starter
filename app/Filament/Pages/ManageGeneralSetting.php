<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\Tabs;

class ManageGeneralSetting extends SettingsPage
{
    protected $listeners = ['$refresh'];

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

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
                                    ->directory('sites')
                                    ->visibility('public')
                                    ->moveFiles()
                                    ->columnStart(1)
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('brand_logoHeight')
                                    ->columnSpan(1),

                                Forms\Components\FileUpload::make('site_favicon')
                                    ->directory('sites')
                                    ->visibility('public')
                                    ->moveFiles()
                                    ->columnStart(1),
                            ]),
                    ]),
                // Forms\Components\Section::make('Site')
                //     ->label('Site')
                //     ->description('Manage basic settings.')
                //     ->icon('fluentui-web-asset-24-o')
                //     ->schema([

                //     ]),
            ]);
    }
}
