<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\Tables\Table;
use Filament\PanelProvider;
use Filament\Infolists\Infolist;
use App\Settings\GeneralSettings;
use Filament\Navigation\MenuItem;
use Illuminate\Support\Facades\Storage;
use App\Filament\Pages\Auth\CustomLogin;
use App\Filament\Pages\Auth\EditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Http\Middleware\Authenticate;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentLaravelLog\FilamentLaravelLogPlugin;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        /**
         * when running test, the settings database is not yet created
         * so we need to cancel components configuration
         */
        if (! app()->runningInConsole()) {
            $this->configureComponents();
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(CustomLogin::class)
            ->profile()
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->url(fn (): string => EditProfile::getUrl())
                    ->label(fn (): string => auth()->user()->name),
            ])

            ->viteTheme('resources/css/filament/admin/theme.css')
            ->sidebarFullyCollapsibleOnDesktop()
            ->maxContentWidth('full')

            ->favicon(fn (GeneralSettings $settings) => Storage::url($settings->site_favicon))
            ->brandName(fn (GeneralSettings $settings) => $settings->brand_name)
            ->brandLogo(fn (GeneralSettings $settings) => blank($settings->brand_logo) ? null : Storage::url($settings->brand_logo))
            ->brandLogoHeight(fn (GeneralSettings $settings) => $settings->brand_logoHeight)
            ->colors(fn (GeneralSettings $settings) => $settings->site_theme)

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')

            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])

            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->plugins([
                $this->enableShieldPlugin(),
                $this->laravelLogPlugin(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function enableShieldPlugin(): FilamentShieldPlugin
    {
        return FilamentShieldPlugin::make()
            ->gridColumns([
                'default' => 1,
                'sm'      => 2,
                'lg'      => 3,
            ])
            ->sectionColumnSpan(1)
            ->checkboxListColumns([
                'default' => 1,
                'sm'      => 2,
                'lg'      => 2,
            ])
            ->resourceCheckboxListColumns([
                'default' => 1,
                'sm'      => 2,
            ]);
    }

    protected function laravelLogPlugin(): FilamentLaravelLogPlugin
    {
        return FilamentLaravelLogPlugin::make()
            ->authorize(fn (): bool => auth()->user()->can('page_ViewLog'))
            ->navigationGroup('Control Panel')
            ->navigationLabel('Logs')
            ->navigationIcon('heroicon-o-bug-ant')
            ->navigationSort(1)
            ->slug('logs');
    }

    public function configureComponents(): void
    {
        /** @var GeneralSettings */
        $settings = app(GeneralSettings::class);

        Table::configureUsing(function (Table $table) use ($settings) {
            $table->paginationPageOptions($settings->pagination);
        });

        Table::$defaultDateTimeDisplayFormat = $settings->date_time_display_format;

        Infolist::$defaultDateTimeDisplayFormat = $settings->date_time_display_format;

        DatePicker::configureUsing(function (DatePicker $datePicker) use ($settings) {
            $datePicker
                ->displayFormat($settings->datepicker_format)
                ->native(false);
        });

        DateTimePicker::configureUsing(function (DateTimePicker $dateTimePicker) use ($settings) {
            $dateTimePicker
                ->minutesStep(5)
                ->seconds(false)
                ->displayFormat($settings->date_time_display_format)
                ->native(false);
        });
    }
}
