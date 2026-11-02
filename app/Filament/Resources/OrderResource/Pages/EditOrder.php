<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\AuditEvent;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn (): bool => auth()->user()?->canManageSettings() ?? false),
        ];
    }

    protected function beforeSave(): void
    {
        if ($this->record->status === 'cancelled' && $this->data['status'] !== 'cancelled') {
            return;
        }
    }

    protected function afterSave(): void
    {
        $newStatus = $this->record->status;
        $actor = auth()->user()?->email ?? 'system';
        AuditEvent::record('order.'.$newStatus, $this->record->id, $actor);
    }
}
