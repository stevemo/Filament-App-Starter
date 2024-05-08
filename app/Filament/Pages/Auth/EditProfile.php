<?php

namespace App\Filament\Pages\Auth;

use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Auth\Authenticatable;

class EditProfile extends Page
{
    protected static string $view = 'filament.pages.auth.edit-profile';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'profile';

    protected static ?string $title = 'My profile';

    public ?array $profileData = [];

    public function mount(): void
    {
        $this->fillForms();
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

    protected function getUpdateProfileFormActions(): array
    {
        return [
            Action::make('updateProfileAction')
                ->label('Save profile')
                ->submit('editProfileForm'),
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

    protected function getForms(): array
    {
        return [
            'editProfileForm',
        ];
    }

    protected function fillForms(): void
    {
        $data = $this->getUser()->attributesToArray();

        $this->editProfileForm->fill($data);
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
