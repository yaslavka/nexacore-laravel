<?php

namespace App\Filament\Resources\LanguageResource\Pages;

use App\Filament\Resources\LanguageResource;
use App\Models\AuditEvent;
use App\Models\Language;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLanguage extends EditRecord
{
    protected static string $resource = LanguageResource::class;

    protected function beforeSave(): void
    {
        // The default language is always active.
        if ($this->record->is_default) {
            $this->data['is_active'] = true;
            $this->record->is_active = true;
        }
    }

    protected function afterSave(): void
    {
        if ($this->record->is_default) {
            static::getModel()::query()
                ->where('code', '!=', $this->record->code)
                ->update(['is_default' => false]);
        }

        Language::forgetCache();
        AuditEvent::record('language.updated', $this->record->code, auth()->user()?->email ?? 'system');

        Notification::make()
            ->title(__('admin.languages.updated'))
            ->body($this->record->native_name)
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return LanguageResource::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn (): bool => ! $this->record->is_default),
        ];
    }
}
