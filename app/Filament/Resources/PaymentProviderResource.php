<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentProviderResource\Pages;
use App\Models\PaymentProvider;
use App\Modules\Payments\PaymentModuleManager;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Payment systems (Payler, demo, …). Each one is a module registered in
 * config/payments.php; adding a new payment system never touches the core.
 */
class PaymentProviderResource extends Resource
{
    protected static ?string $model = PaymentProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.payments');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.payment_providers.nav');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.payment_providers.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.payment_providers.plural');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canManageSettings() ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->canManageSettings() ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->canManageSettings() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('code')
                ->label(__('admin.payment_fields.provider'))
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(40)
                ->alphaDash()
                ->disabled(fn (?PaymentProvider $record) => $record !== null)
                ->dehydrated(fn (?PaymentProvider $record) => $record === null)
                ->helperText(__('admin.payment_fields.key_hint')),
            TextInput::make('name')
                ->label(__('admin.fields.title'))
                ->required()
                ->maxLength(120),
            Select::make('driver')
                ->label(__('admin.payment_fields.driver'))
                ->options(fn (): array => app(PaymentModuleManager::class)->all())
                ->required()
                ->helperText(__('admin.payment_providers.driver_hint')),
            Toggle::make('is_active')
                ->label(__('admin.payment_fields.is_active'))
                ->default(true),
            TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->label(__('admin.languages.sort')),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->label(__('admin.fields.title'))
                        ->weight(FontWeight::Bold)
                        ->size('lg'),
                    TextColumn::make('code')
                        ->badge()
                        ->color('warning'),
                    TextColumn::make('driver')
                        ->label(__('admin.payment_fields.driver'))
                        ->badge()
                        ->color('gray'),
                    IconColumn::make('is_active')
                        ->label(__('admin.payment_fields.is_active'))
                        ->boolean(),
                    TextColumn::make('merchants_count')
                        ->counts('merchants')
                        ->label(__('admin.payment_merchants.plural'))
                        ->badge(),
                ]),
            ])
            ->actions([
                ActionGroup::make([
                    ReplicateAction::make()
                        ->label(__('admin.actions.replicate')),
                ])
                    ->dropdown()
                    ->iconButton()
                    ->dropdownPlacement('bottom-end')
                    ->tooltip(__('admin.actions.more')),
                EditAction::make()->button(),
                DeleteAction::make()->button()->outlined()->color('danger'),
            ])
            ->actionsAlignment(Alignment::End->value)
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'xl' => 3,
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentProviders::route('/'),
            'create' => Pages\CreatePaymentProvider::route('/create'),
            'edit' => Pages\EditPaymentProvider::route('/{record}/edit'),
        ];
    }
}
