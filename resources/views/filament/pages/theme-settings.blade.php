<x-filament-panels::page>
    <form wire:submit="save" class="fi-form">
        {{ $this->form }}

        <div class="fi-form-actions">
            <button type="submit" class="fi-btn fi-btn-color-primary">
                Activate theme
            </button>
        </div>
    </form>
</x-filament-panels::page>
