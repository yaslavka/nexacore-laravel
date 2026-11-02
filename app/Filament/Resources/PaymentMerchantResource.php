<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMerchantResource\Pages;
use App\Models\PaymentMerchant;
use App\Models\PaymentProvider;
use App\Modules\Payments\PaymentModuleManager;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
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
 * Merchants of payment systems. A payment system can have an unlimited
 * number of merchants and every merchant is bound to one site theme —
 * on the web and in the API the theme decides which merchant serves
 * the payment.
 */
class PaymentMerchantResource extends Resource
{
    protected static ?string $model = PaymentMerchant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.payments');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.payment_merchants.nav');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.payment_merchants.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.payment_merchants.plural');
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
            Section::make(__('admin.payment_merchants.singular'))
                ->description(__('admin.payment_merchants.unique_hint'))
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('payment_provider_id')
                            ->label(__('admin.payment_fields.provider'))
                            ->relationship('provider', 'name')
                            ->options(fn () => PaymentProvider::query()->active()->orderBy('sort_order')->pluck('name', 'id'))
                            ->live()
                            ->required()
                            ->searchable(),
                        Select::make('theme_slug')
                            ->label(__('admin.payment_fields.theme'))
                            ->options(function (): array {
                                // Theme objects expose methods, not array keys.
                                $themes = collect(theme_manager()->all())
                                    ->mapWithKeys(fn ($theme, $slug) => [$slug => $theme->name().' ('.$slug.')'])
                                    ->all();

                                return [PaymentMerchant::ANY_THEME => __('admin.payment_fields.wildcard_any_theme')]
                                    + $themes;
                            })
                            ->default(theme()->slug)
                            ->required()
                            ->helperText(__('admin.payment_fields.theme_hint')),
                        TextInput::make('name')
                            ->label(__('admin.fields.title'))
                            ->required()
                            ->maxLength(160)
                            ->columnSpanFull(),
                    ]),
                ]),
            Section::make(__('admin.payment_fields.host'))
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('host')
                            ->label(__('admin.payment_fields.host'))
                            // $record is null on the create page — fall back
                            // to the module default host instead of crashing.
                            ->default(fn (?PaymentMerchant $record) => $record?->host ?: config('payments.payler.host'))
                            ->required()
                            ->maxLength(160)
                            ->helperText(__('admin.payment_fields.host_hint')),
                        Radio::make('mode')
                            ->label(__('admin.payment_fields.mode'))
                            ->options([
                                'live' => __('admin.payment_fields.mode_live'),
                                'test' => __('admin.payment_fields.mode_test'),
                            ])
                            ->default('live')
                            ->inline(),
                        TextInput::make('terminal_key')
                            ->label(__('admin.payment_fields.terminal_key'))
                            ->password()
                            ->revealable()
                            ->maxLength(200)
                            ->helperText(__('admin.payment_fields.key_hint'))
                            // Depends on the selected provider (not $record):
                            // works identically on the create and edit pages.
                            ->required(fn (Get $get): bool => PaymentProvider::find($get('payment_provider_id'))?->driver === 'payler'),
                        TextInput::make('terminal_secret')
                            ->label(__('admin.payment_fields.terminal_secret'))
                            ->password()
                            ->revealable()
                            ->maxLength(200)
                            ->helperText(__('admin.payment_fields.secret_hint')),
                    ]),
                ]),
            Section::make(__('admin.payment_fields.currency'))
                ->schema([
                    Grid::make(3)->schema([
                        Select::make('currency')
                            ->label(__('admin.payment_fields.currency'))
                            ->options([
                                'KGS' => 'KGS — Киргизский сом',
                                'USD' => 'USD — Доллар США',
                                'RUB' => 'RUB — Российский рубль',
                                'EUR' => 'EUR — Евро',
                                'GBP' => 'GBP — Британский фунт',
                                'TJS' => 'TJS — Таджикский сомони',
                                'KRW' => 'KRW — Южнокорейская вона',
                                'INR' => 'INR — Индийская рупия',
                                'AED' => 'AED — Дирхам ОАЭ',
                                'AZN' => 'AZN — Азербайджанский манат',
                                'BRL' => 'BRL — Бразильский реал',
                                'CAD' => 'CAD — Канадский доллар',
                                'PLN' => 'PLN — Польский злотый',
                            ])
                            ->default('KGS')
                            ->required(),
                        Radio::make('operation_type')
                            ->label(__('admin.payment_fields.operation_type'))
                            ->options([
                                'OneStep' => __('admin.payment_fields.one_step'),
                                'TwoStep' => __('admin.payment_fields.two_step'),
                            ])
                            ->default('OneStep')
                            ->inline(),
                        TextInput::make('lifetime_minutes')
                            ->label(__('admin.payment_fields.lifetime'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10080)
                            ->default(60),
                    ]),
                    Grid::make(2)->schema([
                        Toggle::make('is_active')
                            ->label(__('admin.payment_fields.is_active'))
                            ->default(true),
                        Placeholder::make('callback_url')
                            ->label(__('admin.payment_fields.callback_url'))
                            ->content(function (?PaymentMerchant $record): string {
                                $code = $record?->provider?->code;

                                $url = $code
                                    ? url('/api/payments/'.$code.'/callback')
                                    : url('/api/payments/{provider}/callback');

                                return $url.' — '.__('admin.payment_fields.callback_hint');
                            })
                            ->columnSpanFull(),
                    ]),
                ]),
        ]);
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
                    TextColumn::make('provider.name')
                        ->label(__('admin.payment_fields.provider'))
                        ->badge()
                        ->color('warning'),
                    TextColumn::make('theme_slug')
                        ->label(__('admin.payment_fields.theme'))
                        ->badge()
                        ->color(fn (string $state): string => $state === PaymentMerchant::ANY_THEME ? 'gray' : 'success')
                        ->formatStateUsing(fn (string $state): string => $state === PaymentMerchant::ANY_THEME
                            ? __('admin.payment_fields.wildcard_any_theme')
                            : $state),
                    TextColumn::make('mode')
                        ->badge()
                        ->color(fn (string $state): string => $state === 'test' ? 'warning' : 'success')
                        ->formatStateUsing(fn (string $state): string => $state === 'test'
                            ? __('admin.payment_fields.mode_test')
                            : __('admin.payment_fields.mode_live')),
                    TextColumn::make('host')
                        ->label(__('admin.payment_fields.host'))
                        ->copyable()
                        ->placeholder('—'),
                    IconColumn::make('is_active')
                        ->label(__('admin.payment_fields.is_active'))
                        ->boolean(),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentMerchants::route('/'),
            'create' => Pages\CreatePaymentMerchant::route('/create'),
            'edit' => Pages\EditPaymentMerchant::route('/{record}/edit'),
        ];
    }
}
