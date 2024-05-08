<?php

namespace App\Filament\Pages\Auth;

use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Password;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Auth\Authenticatable;

class EditProfile extends Page
{
    protected static string $view = 'filament.pages.auth.edit-profile';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'profile';

    protected static ?string $title = 'My profile';

    public ?array $profileData = [];

    public ?array $passwordData = [];

    public function mount(): void
    {
        $this->fillForms();
    }

    protected function getForms(): array
    {
        return [
            'editProfileForm',
            'editPasswordForm',
        ];
    }

    protected function fillForms(): void
    {
        $data = $this->getUser()->attributesToArray();

        $this->editProfileForm->fill($data);
        $this->editPasswordForm->fill();
    }

    public function editProfileForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->aside()
                    ->description('Update your account\'s profile information and email address.')
                    ->schema([
                        Forms\Components\Grid::make(['sm' => 1, 'md' => 4])
                            ->schema([
                                Forms\Components\FileUpload::make('avatar')
                                    ->disk('avatar')
                                    ->avatar()
                                    ->imageEditor()
                                    ->columnSpan(1),

                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('name')
                                        ->required(),
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->required()
                                        ->unique(ignoreRecord: true),
                                    Forms\Components\TextInput::make('username')
                                        ->unique(ignoreRecord: true)
                                        ->maxLength(255),
                                ])->columnSpan(['sm' => 1, 'md' => 2]),
                            ]),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('profileData');
    }

    public function editPasswordForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Update Password')
                    ->aside()
                    ->description('Ensure your account is using long, random password to stay secure.')
                    ->schema([
                        Forms\Components\TextInput::make('Current password')
                            ->password()
                            ->required()
                            ->currentPassword(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->rule(Password::default()->min(6))
                            ->revealable()
                            ->autocomplete('new-password')
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation'),
                        Forms\Components\TextInput::make('passwordConfirmation')
                            ->password()
                            ->required()
                            ->revealable()
                            ->dehydrated(false),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('passwordData');
    }

    protected function getUpdateProfileFormActions(): array
    {
        return [
            Action::make('updateProfileAction')
                ->label('Save profile')
                ->submit('editProfileForm'),
        ];
    }

    protected function getUpdatePasswordFormActions(): array
    {
        return [
            Action::make('updatePasswordAction')
                ->label('Save password')
                ->submit('editPasswordForm'),
        ];
    }

    public function updateProfile(): void
    {
        try {
            $data = $this->editProfileForm->getState();

            $this->handleRecordUpdate($this->getUser(), $data);
        } catch (Halt $exception) {
            return;
        }

        $this->sendSuccessNotification('Profile saved successfully!');

        $this->redirect($this->getUrl(), navigate: FilamentView::hasSpaMode() && is_app_url($this->getUrl()));
    }

    public function updatePassword(): void
    {
        try {
            $data = $this->editPasswordForm->getState();

            unset($data['Current password']);

            $this->handleRecordUpdate($this->getUser(), $data);
        } catch (Halt $exception) {
            return;
        }

        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put([
                'password_hash_'.Filament::getAuthGuard() => $data['password'],
            ]);
        }

        $this->editPasswordForm->fill();

        $this->sendSuccessNotification('New password saved successfully!');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    private function sendSuccessNotification(string $message): void
    {
        Notification::make()
            ->success()
            ->title($message)
            ->send();
    }

    protected function getUser(): Authenticatable&Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }
}
