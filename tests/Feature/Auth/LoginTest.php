<?php

use App\Models\User;

use function Pest\Livewire\livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Filament\Pages\Auth\CustomLogin;

it('can login using email', function () {
    $user = User::factory()->create([
        'name'     => 'test user',
        'email'    => 'steve@stevemo.ca',
        'password' => Hash::make('secret'),
    ]);

    livewire(CustomLogin::class)
        ->fillForm([
            'login'    => $user->email,
            'password' => 'secret',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(Auth::check())->toBeTrue();
});

it('can login using username', function () {
    User::factory()->create([
        'name'     => 'test user',
        'email'    => 'steve@stevemo.ca',
        'username' => 'stevemo',
        'password' => Hash::make('secret'),
    ]);

    livewire(CustomLogin::class)
        ->fillForm([
            'login'    => 'stevemo',
            'password' => 'secret',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(Auth::check())->toBeTrue();
});
