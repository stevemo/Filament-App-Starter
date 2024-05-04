<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSetting extends Settings
{
    public ?string $brand_name;

    public ?string $brand_logo;

    public ?string $brand_logoHeight;

    public ?string $site_favicon;

    public static function group(): string
    {
        return 'general';
    }
}
