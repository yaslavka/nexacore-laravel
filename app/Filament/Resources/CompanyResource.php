<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\AuditEvent;
use App\Models\Company;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Companies of the workspace. There can be any number of companies; every
 * card shows the legal details, exactly one company is «primary» — its
 * details are shown on the public website, in invoices and in
 * GET /api/settings (see App\Models\Setting::current()).
 */
class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.companies.nav');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.companies.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.companies.plural');
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
            Section::make(__('admin.companies.section_general'))
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label(__('admin.fields.company_name'))
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),
                        TextInput::make('legal_entity')
                            ->label(__('admin.fields.legal_entity'))
                            ->maxLength(1000),
                        TextInput::make('tagline')
                            ->label(__('admin.fields.tagline'))
                            ->maxLength(500),
                        TextInput::make('email')
                            ->label(__('admin.fields.email'))
                            ->email()
                            ->maxLength(254),
                        TextInput::make('phone')
                            ->label(__('admin.fields.phone'))
                            ->maxLength(100),
                        TextInput::make('address')
                            ->label(__('admin.fields.address'))
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Toggle::make('is_default')
                            ->label(__('admin.companies.is_default'))
                            ->helperText(__('admin.companies.is_default_hint'))
                            ->columnSpanFull(),
                    ]),
                ]),
            Section::make(__('admin.companies.section_legal'))
                ->schema([
                    Textarea::make('privacy_text')
                        ->rows(6)
                        ->maxLength(12000)
                        ->label(__('admin.companies.privacy'))
                        ->helperText(__('admin.companies.privacy_hint')),
                    Textarea::make('terms_text')
                        ->rows(6)
                        ->maxLength(12000)
                        ->label(__('admin.companies.terms'))
                        ->helperText(__('admin.companies.terms_hint')),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->label(__('admin.fields.company_name'))
                        ->weight(FontWeight::Bold)
                        ->size('lg'),
                    // Badges live in one horizontal row right under the title.
                    Split::make([
                        TextColumn::make('is_default')
                            ->label(__('admin.companies.is_default'))
                            ->badge()
                            ->color(fn (bool $state): string => $state ? 'warning' : 'gray')
                            ->formatStateUsing(fn (bool $state): string => $state
                                ? __('admin.companies.badge_primary')
                                : __('admin.companies.badge_secondary')),
                        TextColumn::make('tagline')
                            ->label(__('admin.fields.tagline'))
                            ->badge()
                            ->color('success')
                            ->placeholder(''),
                    ]),
                    TextColumn::make('legal_entity')
                        ->label(__('admin.fields.legal_entity'))
                        ->copyable()
                        ->placeholder('—'),
                    TextColumn::make('email')
                        ->label(__('admin.fields.email'))
                        ->copyable()
                        ->placeholder('—'),
                    TextColumn::make('phone')
                        ->label(__('admin.fields.phone'))
                        ->copyable()
                        ->placeholder('—'),
                    TextColumn::make('address')
                        ->label(__('admin.fields.address'))
                        ->placeholder('—'),
                ]),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('make_primary')
                        ->label(__('admin.companies.make_primary'))
                        ->icon('heroicon-m-star')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalDescription(fn (Company $record): string => __(
                            'admin.companies.make_primary_body',
                            ['name' => $record->name],
                        ))
                        ->visible(fn (Company $record): bool => ! $record->is_default)
                        ->action(function (Company $record): void {
                            $record->update(['is_default' => true]);

                            AuditEvent::record(
                                'company.primary',
                                $record->name,
                                auth()->user()?->email ?? 'system',
                            );

                            Notification::make()
                                ->title(__('admin.companies.made_primary'))
                                ->body(__('admin.companies.made_primary_body', ['name' => $record->name]))
                                ->success()
                                ->send();
                        }),
                ])
                    ->dropdown()
                    ->iconButton()
                    ->dropdownPlacement('bottom-end')
                    ->tooltip(__('admin.actions.more'))
                    ->visible(fn (Company $record): bool => ! $record->is_default),
                EditAction::make()->button(),
                DeleteAction::make()
                    ->button()
                    ->outlined()
                    ->color('danger')
                    ->before(function (DeleteAction $action): void {
                        // The workspace must always keep at least one company.
                        if (Company::query()->count() <= 1) {
                            Notification::make()
                                ->title(__('admin.companies.last_company'))
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    })
                    ->after(function (): void {
                        AuditEvent::record(
                            'company.deleted',
                            '',
                            auth()->user()?->email ?? 'system',
                        );
                    }),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'xl' => 3,
            ])
            ->defaultSort('is_default', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
