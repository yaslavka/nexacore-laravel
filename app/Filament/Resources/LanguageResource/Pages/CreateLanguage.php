<?php

namespace App\Filament\Resources\LanguageResource\Pages;

use App\Filament\Resources\LanguageResource;
use App\Models\AuditEvent;
use App\Models\Language;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateLanguage extends CreateRecord
{
    protected static string $resource = LanguageResource::class;

    protected function handleRecordCreation(array $data): Language
    {
        if (! empty($data['is_default'])) {
            $data['is_active'] = true;
        }

        /** @var Language $language */
        $language = static::getModel()::create($data);

        if ($language->is_default) {
            static::getModel()::query()
                ->where('code', '!=', $language->code)
                ->update(['is_default' => false]);
        }

        Language::forgetCache();
        AuditEvent::record('language.created', $language->code, auth()->user()?->email ?? 'system');

        Notification::make()
            ->title(__('admin.languages.created'))
            ->body($language->native_name)
            ->success()
            ->send();

        return $language;
    }

    protected function getRedirectUrl(): string
    {
        return LanguageResource::getUrl('index');
    }
}
