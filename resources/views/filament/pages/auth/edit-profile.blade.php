<x-filament-panels::page>
    <x-filament-panels::form wire:submit="updateProfile">
        {{ $this->editProfileForm }}
        <x-filament-panels::form.actions
            alignment="end"
            :actions="$this->getUpdateProfileFormActions()"
        />
    </x-filament-panels::form>

    <x-filament-panels::form wire:submit="updatePassword">
        {{ $this->editPasswordForm }}
        <x-filament-panels::form.actions
            alignment="end"
            :actions="$this->getUpdatePasswordFormActions()"
        />
    </x-filament-panels::form>

</x-filament-panels::page>
