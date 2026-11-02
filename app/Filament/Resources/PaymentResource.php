<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Order;
use App\Models\Payment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.orders');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.payments.nav');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.payments.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.payments.plural');
    }

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
                    ->label(__('admin.resources.orders.nav'))
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
                    ->label(__('admin.fields.amount'))
                    ->numeric()
                    ->minValue(0.01)
                    ->required()
                    ->helperText(__('admin.fields.amount_helper'))
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('order.reference')
                        ->label(__('admin.resources.orders.nav'))
                        ->searchable()
                        ->weight(FontWeight::Bold)
                        ->size('lg'),
                    TextColumn::make('id')->label(__('admin.fields.token'))->limit(10)->copyable(),
                    TextColumn::make('amount')
                        ->label(__('admin.fields.amount'))
                        ->formatStateUsing(fn ($record): string => number_format($record->amount / 100, 2).' '.$record->currency),
                    TextColumn::make('mode')
                        ->label(__('admin.fields.mode'))
                        ->badge()
                        ->color(fn (string $state) => $state === 'sandbox' ? 'warning' : ($state === 'off' ? 'gray' : 'success')),
                    TextColumn::make('status')
                        ->label(__('admin.fields.status'))
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => __('admin.statuses.'.$state) !== 'admin.statuses.'.$state ? __('admin.statuses.'.$state) : $state)
                        ->color(fn (string $state) => match ($state) {
                            'paid' => 'success',
                            'cancelled', 'expired' => 'danger',
                            'pending' => 'warning',
                            default => 'info',
                        }),
                    TextColumn::make('created_at')
                        ->label(__('admin.fields.created_at'))
                        ->dateTime('d M Y, H:i')
                        ->sortable(),
                ]),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                ])
                    ->dropdown()
                    ->iconButton()
                    ->dropdownPlacement('bottom-end')
                    ->tooltip(__('admin.actions.more')),
                DeleteAction::make()
                    ->button()
                    ->outlined()
                    ->color('danger')
                    ->visible(fn (): bool => auth()->user()?->canManageSettings() ?? false),
            ])
            ->actionsAlignment(Alignment::End)
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'xl' => 3,
            ])
            ->paginated([10, 25, 50])
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
