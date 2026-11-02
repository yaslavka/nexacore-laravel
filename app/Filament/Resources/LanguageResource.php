<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LanguageResource\Pages;
use App\Models\Language;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Support\Enums\FontWeight;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.languages.nav');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.languages.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.languages.plural');
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->label(__('admin.languages.code'))
                    ->required()
                    ->maxLength(12)
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->helperText(__('admin.languages.code_hint'))
                    ->columnSpan(1),
                TextInput::make('name')
                    ->label(__('admin.languages.name'))
                    ->required()
                    ->maxLength(100)
                    ->columnSpan(1),
                TextInput::make('native_name')
                    ->label(__('admin.languages.native'))
                    ->required()
                    ->maxLength(100)
                    ->columnSpan(1),
                TextInput::make('sort_order')
                    ->label(__('admin.languages.sort'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(999)
                    ->default(fn () => (int) (Language::query()->max('sort_order') ?? 0) + 1)
                    ->columnSpan(1),
                Toggle::make('is_active')
                    ->label(__('admin.languages.active'))
                    ->default(true)
                    ->live()
                    ->columnSpan(1),
                Toggle::make('is_default')
                    ->label(__('admin.languages.default'))
                    ->default(false)
                    ->live()
                    ->helperText(__('admin.languages.default_hint'))
                    ->columnSpan(1),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('native_name')
                        ->label(__('admin.languages.native'))
                        ->weight(FontWeight::Bold)
                        ->size('lg'),
                    TextColumn::make('code')
                        ->label(__('admin.languages.code'))
                        ->badge()
                        ->color('primary')
                        ->copyable(),
                    TextColumn::make('name')
                        ->label(__('admin.languages.name')),
                    IconColumn::make('is_active')
                        ->label(__('admin.languages.active'))
                        ->boolean(),
                    IconColumn::make('is_default')
                        ->label(__('admin.languages.default'))
                        ->boolean(),
                    TextColumn::make('sort_order')
                        ->label(__('admin.languages.sort'))
                        ->sortable(),
                ]),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('make_default')
                        ->label(__('admin.languages.make_default'))
                        ->icon('heroicon-o-star')
                        ->visible(fn (Language $record): bool => $record->is_active && (! $record->is_default))
                        ->requiresConfirmation()
                        ->action(function (Language $record): void {
                            \Illuminate\Support\Facades\DB::table('languages')->update(['is_default' => false]);
                            $record->forceFill(['is_default' => true])->save();

                            \Filament\Notifications\Notification::make()
                                ->title(__('admin.languages.default_changed'))
                                ->success()
                                ->send();
                        }),
                ])
                    ->dropdown()
                    ->iconButton()
                    ->dropdownPlacement('bottom-end')
                    ->tooltip(__('admin.actions.more')),
                EditAction::make()->button(),
                DeleteAction::make()->button()->outlined()->color('danger'),
            ])
            ->actionsAlignment(\Filament\Support\Enums\Alignment::End->value)
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
        return (auth()->user()?->canManageSettings() ?? false)
            && ! ($record instanceof Language && $record->is_default);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit' => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }
}
