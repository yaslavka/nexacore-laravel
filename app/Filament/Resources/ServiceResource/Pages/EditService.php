<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\AuditEvent;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->hidden(),
        ];
    }

    protected function beforeSave(): void
    {
        if (array_key_exists('price', $this->data)) {
            $price = $this->data['price'] === '' || $this->data['price'] === null ? null : (float) $this->data['price'];
            $this->record->price = $price;
            $this->record->price_label = $price === null
                ? 'Custom quote'
                : 'From $'.number_format($price);

            if (is_array($this->record->packages) && count($this->record->packages)) {
                $packages = $this->record->packages;
                $packages[0]['price'] = $price;
                $this->record->packages = $packages;
            }
        }
    }

    protected function afterSave(): void
    {
        AuditEvent::record('service.updated', $this->record->id, auth()->user()?->email ?? 'system');
    }
}
