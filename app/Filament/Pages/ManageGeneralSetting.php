<?php

namespace App\Filament\Pages;

use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\ToggleButtons;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageGeneralSetting extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Control Panel';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Settings';

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
                    ->contained(false)
                    ->columnSpanFull()
                    ->tabs([
                        $this->siteTab(),
                        $this->colorTab(),
                        Tabs\Tab::make('Components')
                            ->icon('fluentui-puzzle-piece-24')
                            ->columns(4)
                            ->schema([
                                ToggleButtons::make('pagination')
                                    ->hint('To customize the default number of records shown')
                                    ->multiple()
                                    ->inline()
                                    ->required()
                                    ->columnSpan(2)
                                    ->options([
                                        'all' => 'All',
                                        10    => 10,
                                        25    => 25,
                                        50    => 50,
                                        75    => 75,
                                    ]),

                                Radio::make('date_time_display_format')
                                    ->required()
                                    ->columns(2)
                                    ->columnSpan(3)
                                    ->columnStart(1)
                                    ->options([
                                        'M j, Y H:i' => now()->format('M j, Y H:i'),
                                        'M j, Y h:i' => now()->format('M j, Y h:i'),
                                        'j M, Y H:i' => now()->format('j M, Y H:i'),
                                        'j M, Y h:i' => now()->format('j M, Y h:i'),
                                    ])
                                    ->descriptions([
                                        'M j, Y H:i' => 'Month Day Year in 24 Hours Format',
                                        'M j, Y h:i' => 'Month Day Year in 12 Hours Format',
                                        'j M, Y H:i' => 'Day Month Year in 24 Hours Format',
                                        'j M, Y h:i' => 'Day Month Year in 12 Hours Format',
                                    ]),

                                Radio::make('datepicker_format')
                                    ->required()
                                    ->columns(2)
                                    ->columnSpan(3)
                                    ->columnStart(1)
                                    ->options([
                                        'M j, Y' => now()->format('M j, Y'),
                                        'j M, Y' => now()->format('j M, Y'),
                                    ])
                                    ->descriptions([
                                        'M j, Y' => 'Month Day Year',
                                        'j M, Y' => 'Day Month Year',
                                    ]),
                            ]),
                    ]),
            ]);
    }

    protected function siteTab(): Tabs\Tab
    {
        return Tabs\Tab::make('Site')
            ->icon('fluentui-web-asset-24-o')
            ->schema([
                Grid::make(['md' => 2])
                    ->schema([
                        Group::make([
                            Section::make('Site Name')
                                ->icon('fluentui-home-person-24')
                                ->schema([
                                    TextInput::make('brand_name')
                                        ->hiddenLabel()
                                        ->required(),
                                ])->columnSpan(1),
                            Section::make('Favicon')
                                ->icon('fluentui-circle-image-28')
                                ->schema([
                                    FileUpload::make('site_favicon')
                                        ->hiddenLabel()
                                        ->directory('settings')
                                        ->moveFiles()
                                        ->columnStart(1)
                                        ->deleteUploadedFileUsing(fn (Get $get) => Storage::delete(app(GeneralSettings::class)->site_favicon)),
                                ]),
                        ]),

                        Group::make([
                            Section::make('Site Logo')
                                ->icon('fluentui-image-28-o')
                                ->columns(3)
                                ->schema([
                                    FileUpload::make('brand_logo')
                                        ->columnSpanFull()
                                        ->hiddenLabel()
                                        ->directory('settings')
                                        ->moveFiles()
                                        ->deleteUploadedFileUsing(fn (Get $get) => Storage::delete(app(GeneralSettings::class)->brand_logo)),

                                    TextInput::make('brand_logoHeight')
                                        ->label('Logo Height')
                                        ->columnSpan(1)
                                        ->suffix('rem')
                                        ->formatStateUsing(fn (string $state): string => str($state)->remove('rem'))
                                        ->dehydrateStateUsing(fn (string $state): string => $state.'rem'),
                                ]),
                        ]),
                    ]),
            ]);
    }

    protected function colorTab(): Tabs\Tab
    {
        return Tabs\Tab::make('Colors')
            ->icon('fluentui-color-24-o')
            ->schema([
                Grid::make(2)
                    ->schema([
                        Group::make([
                            Section::make('Main Colors')
                                ->icon('fluentui-color-24-o')
                                ->schema([
                                    ColorPicker::make('site_theme.primary')->rgb(),
                                    ColorPicker::make('site_theme.secondary')->rgb(),
                                    ColorPicker::make('site_theme.gray')->rgb(),
                                ]),
                        ]),
                        Group::make([
                            Section::make('Notification Colors')
                                ->icon('fluentui-color-24-o')
                                ->schema([
                                    ColorPicker::make('site_theme.success')->rgb(),
                                    ColorPicker::make('site_theme.danger')->rgb(),
                                    ColorPicker::make('site_theme.info')->rgb(),
                                    ColorPicker::make('site_theme.warning')->rgb(),
                                ]),
                        ]),
                    ]),
            ]);
    }
}
