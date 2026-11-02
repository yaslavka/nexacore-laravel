<x-filament-panels::page>
    <form wire:submit="save" class="fi-form">
        {{ $this->form }}

        <div class="fi-form-actions">
            <x-filament::button type="submit" color="primary">
                {{ __('admin.pages.language.apply') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
