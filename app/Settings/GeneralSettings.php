<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $brand_name;

    public ?string $brand_logo;

    public ?string $brand_logoHeight;

    public ?string $site_favicon;

    public array $site_theme;

    public array $pagination;

    public string $default_date_time_display_format;

    public string $datepicker_format;

    public static function group(): string
    {
        return 'general';
    }
}
