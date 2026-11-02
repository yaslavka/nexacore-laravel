<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\AuditEvent;
use App\Models\Order;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Orders';

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationLabel = 'Enquiries';

    protected static ?string $pluralModelLabel = 'Enquiries';

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->canEditOrders() ?? false;
    }

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('reference')->disabled(),
                TextInput::make('service_title')->label('Service')->disabled(),
                TextInput::make('package_name')->label('Package')->disabled(),
                TextInput::make('name')->disabled(),
                TextInput::make('email')->disabled(),
                TextInput::make('company')->disabled(),
                Textarea::make('message')->disabled()->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'new' => 'New',
                        'in_progress' => 'In progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                TextInput::make('payment_status')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('service_title')
                    ->label('Service')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('package_name')
                    ->label('Package')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'New',
                        'in_progress' => 'In progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        default => $state,
                    }),
                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => str_contains($state, 'paid') ? 'success' : 'gray'),
                TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'in_progress' => 'In progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('invoice')
                    ->label('Payment link')
                    ->icon('heroicon-o-banknotes')
                    ->visible(fn (): bool => auth()->user()?->canManageSettings() ?? false)
                    ->url(fn (Order $record): string => PaymentResource::getUrl('create', ['order' => $record->id]))
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
