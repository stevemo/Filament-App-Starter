<?php

use App\Models\User;

use function Pest\Livewire\livewire;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;

it('can create new user', function () {
    login(['view_any_user', 'create_user']);

    livewire(CreateUser::class)
        ->fillForm([
            'name'                 => 'User',
            'email'                => 'user@test.com',
            'password'             => 'password',
            'passwordConfirmation' => 'password',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect(UserResource::getUrl('index'))
        ->assertNotified();

    $user = User::whereName('User')->first();

    expect($user)
        ->name->toBe('User')
        ->email->toBe('user@test.com');
});
