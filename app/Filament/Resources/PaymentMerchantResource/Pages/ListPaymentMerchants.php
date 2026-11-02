<?php

namespace App\Filament\Resources\PaymentMerchantResource\Pages;

use App\Filament\Resources\PaymentMerchantResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaymentMerchants extends ListRecords
{
    protected static string $resource = PaymentMerchantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // «New merchant» — the same standard header button every
            // standard list page has.
            CreateAction::make(),
        ];
    }
}
