<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;

use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.orders');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.orders.nav');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.orders.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.orders.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Order::query()->where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

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
                TextInput::make('service_title')->label(__('admin.fields.service'))->disabled(),
                TextInput::make('package_name')->label(__('admin.fields.package'))->disabled(),
                TextInput::make('name')->label(__('admin.fields.name'))->disabled(),
                TextInput::make('email')->label(__('admin.fields.email'))->disabled(),
                TextInput::make('company')->label(__('admin.fields.company'))->disabled(),
                Textarea::make('message')->label(__('admin.fields.message'))->disabled()->columnSpanFull(),
                Select::make('status')
                    ->label(__('admin.fields.status'))
                    ->options(self::statusOptions())
                    ->required(),
                TextInput::make('payment_status')
                    ->label(__('admin.fields.payment_status'))
                    ->disabled(),
            ]);
    }

    public static function statusOptions(): array
    {
        return [
            'new' => __('admin.statuses.new'),
            'in_progress' => __('admin.statuses.in_progress'),
            'completed' => __('admin.statuses.completed'),
            'cancelled' => __('admin.statuses.cancelled'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('reference')
                        ->label(__('admin.fields.reference'))
                        ->searchable()
                        ->copyable()
                        ->weight(FontWeight::Bold)
                        ->size('lg'),
                TextColumn::make('service_title')
                    ->label(__('admin.fields.service'))
                    ->searchable()
                    ->limit(30),
                TextColumn::make('package_name')
                    ->label(__('admin.fields.package'))
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('admin.fields.email'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state),
                TextColumn::make('payment_status')
                    ->label(__('admin.fields.payment'))
                    ->badge()
                    ->color(fn (string $state): string => str_contains($state, 'paid') ? 'success' : 'gray'),
                    TextColumn::make('created_at')
                        ->label(__('admin.fields.created_at'))
                        ->dateTime('d M Y, H:i')
                        ->sortable(),
                ]),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.fields.status'))
                    ->options(self::statusOptions()),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('invoice')
                    ->label(__('admin.order_actions.payment_link'))
                    ->icon('heroicon-o-banknotes')
                    ->visible(fn (): bool => auth()->user()?->canManageSettings() ?? false)
                    ->url(fn (Order $record): string => PaymentResource::getUrl('create', ['order' => $record->id]))
                    ->color('gray'),
            ])
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'xl' => 3,
            ])
            ->paginated([10, 25, 50])
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
