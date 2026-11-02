<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Order;
use App\Models\Payment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationGroup = 'Orders';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Payments';

    protected static ?string $pluralModelLabel = 'Payments';

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->canManageSettings() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('order_id')
                    ->label('Enquiry')
                    ->options(fn () => Order::query()
                        ->where('status', '!=', 'cancelled')
                        ->orderByDesc('created_at')
                        ->limit(200)
                        ->get()
                        ->mapWithKeys(fn (Order $order) => [
                            $order->id => "{$order->reference} — {$order->service_title} ({$order->email})",
                        ]))
                    ->searchable()
                    ->required()
                    ->disabled(fn (?string $operation) => $operation === 'edit')
                    ->dehydrated(fn (?string $operation) => $operation === 'create'),
                TextInput::make('amount_major')
                    ->label('Amount (USD)')
                    ->numeric()
                    ->minValue(0.01)
                    ->required()
                    ->helperText('The amount is agreed with the client before invoicing.')
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Token')->limit(10)->copyable(),
                TextColumn::make('order.reference')->label('Enquiry')->searchable(),
                TextColumn::make('amount')
                    ->formatStateUsing(fn ($record): string => number_format($record->amount / 100, 2).' '.$record->currency),
                TextColumn::make('mode')->badge()->color(fn (string $state) => $state === 'sandbox' ? 'warning' : ($state === 'off' ? 'gray' : 'success')),
                TextColumn::make('status')->badge()->color(fn (string $state) => match ($state) {
                    'paid' => 'success',
                    'cancelled', 'expired' => 'danger',
                    'pending' => 'warning',
                    default => 'info',
                }),
                TextColumn::make('created_at')->dateTime('d M Y, H:i')->sortable(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
