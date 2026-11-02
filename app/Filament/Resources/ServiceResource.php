<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.catalog');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.services.nav');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.services.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.services.plural');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->canEditCatalog() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    TextInput::make('id')->disabled()->label(__('admin.fields.id')),
                    TextInput::make('category_id')->disabled()->label(__('admin.fields.category')),
                    TextInput::make('duration')->maxLength(200)->label(__('admin.tabs.duration_en')),
                ]),
                Tabs::make(__('admin.tabs.translations'))->tabs([
                    Tabs\Tab::make(__('admin.tabs.english'))
                        ->schema([
                            TextInput::make('title')->required()->maxLength(200)->columnSpanFull()->label(__('admin.fields.title')),
                            Textarea::make('description')->required()->maxLength(3000)->columnSpanFull()->label(__('admin.fields.description')),
                            TextInput::make('price')
                                ->numeric()
                                ->minValue(0)
                                ->nullable()
                                ->prefix('$')
                                ->label(__('admin.fields.price'))
                                ->helperText(__('admin.fields.price_helper')),
                            TagsInput::make('deliverables')
                                ->label(__('admin.fields.deliverables'))
                                ->suggestions(['Page structure', 'Responsive pages', 'Contact form'])
                                ->columnSpanFull(),
                            Repeater::make('packages')
                                ->label(__('admin.fields.packages'))
                                ->schema([
                                    TextInput::make('name')->required()->maxLength(160)->label(__('admin.fields.name')),
                                    Textarea::make('description')->required()->maxLength(3000)->columnSpan(2)->label(__('admin.fields.description')),
                                    TextInput::make('price')->numeric()->minValue(0)->nullable()->prefix('$')->label(__('admin.fields.price')),
                                ])
                                ->columns(4)
                                ->columnSpanFull(),
                        ]),
                    Tabs\Tab::make(__('admin.tabs.russian'))
                        ->schema([
                            TextInput::make('ru.title')->maxLength(200)->label(__('admin.tabs.title_ru')),
                            Textarea::make('ru.description')->maxLength(3000)->columnSpanFull()->label(__('admin.tabs.desc_ru')),
                            TextInput::make('ru.duration')->maxLength(200)->label(__('admin.tabs.duration_ru')),
                        ])
                        ->badge(fn ($record): string => !empty($record?->ru['title']) ? __('admin.tabs.translated') : __('admin.tabs.empty')),
                    Tabs\Tab::make(__('admin.tabs.kyrgyz'))
                        ->schema([
                            TextInput::make('ky.title')->maxLength(200)->label(__('admin.tabs.title_ky')),
                            Textarea::make('ky.description')->maxLength(3000)->columnSpanFull()->label(__('admin.tabs.desc_ky')),
                            TextInput::make('ky.duration')->maxLength(200)->label(__('admin.tabs.duration_ky')),
                        ])
                        ->badge(fn ($record): string => !empty($record?->ky['title']) ? __('admin.tabs.translated') : __('admin.tabs.empty')),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('title')
                        ->label(__('admin.fields.title'))
                        ->limit(40)
                        ->searchable()
                        ->weight(FontWeight::Bold)
                        ->size('lg'),
                    TextColumn::make('id')->label(__('admin.fields.id'))->limit(12),
                    TextColumn::make('category_id')->label(__('admin.fields.category')),
                    TextColumn::make('price')
                        ->label(__('admin.fields.price'))
                        ->formatStateUsing(fn ($state): string => $state === null ? __('admin.fields.custom_quote') : '$'.number_format((float) $state)),
                    TextColumn::make('ru')
                        ->formatStateUsing(fn ($record): string => !empty($record->ru['title']) ? '✓ ru' : '— ru')
                        ->badge()
                        ->color(fn ($record): string => !empty($record->ru['title']) ? 'success' : 'gray'),
                    TextColumn::make('ky')
                        ->formatStateUsing(fn ($record): string => !empty($record->ky['title']) ? '✓ ky' : '— ky')
                        ->badge()
                        ->color(fn ($record): string => !empty($record->ky['title']) ? 'success' : 'gray'),
                ]),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('open')
                        ->label(__('admin.actions.open_on_site'))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (Service $record): string => url('/service/'.$record->id))
                        ->openUrlInNewTab(),
                ])
                    ->dropdown()
                    ->iconButton()
                    ->dropdownPlacement('bottom-end')
                    ->tooltip(__('admin.actions.more')),
                EditAction::make()->button(),
            ])
            ->actionsAlignment(Alignment::End->value)
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'xl' => 3,
            ])
            ->paginated([10, 25, 50])
            ->searchable();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
