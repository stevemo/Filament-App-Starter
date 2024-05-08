<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Page;

class EditProfile extends Page
{
    protected static string $view = 'filament.pages.auth.edit-profile';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'profile';

    protected static ?string $title = 'My profile';
}
