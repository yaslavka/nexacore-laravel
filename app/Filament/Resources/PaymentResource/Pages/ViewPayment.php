<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Payment')->schema([
                TextEntry::make('id')->label('Token')->copyable(),
                TextEntry::make('order.reference')->label('Enquiry reference'),
                TextEntry::make('amount')->formatStateUsing(fn ($record) => number_format($record->amount / 100, 2).' '.$record->currency),
                TextEntry::make('mode')->badge(),
                TextEntry::make('status')->badge(),
                TextEntry::make('expires_at')->dateTime('d M Y, H:i'),
                TextEntry::make('paid_at')->dateTime('d M Y, H:i')->placeholder('—'),
                TextEntry::make('checkout_url')
                    ->label('Checkout link')
                    ->state(fn ($record) => url('/checkout/'.$record->id))
                    ->copyable(),
                TextEntry::make('payment_url')
                    ->label('Gateway link')
                    ->copyable()
                    ->copyMessage('Gateway link copied')
                    ->placeholder('—'),
                TextEntry::make('client_email')
                    ->label('Client email')
                    ->state(fn ($record) => $record->payload['email'] ?? $record->order?->email ?? '—'),
            ])->columns(2),
        ]);
    }
}
