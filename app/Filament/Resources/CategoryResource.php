<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.catalog');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.directions.nav');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.directions.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.directions.plural');
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
                TextInput::make('id')->disabled()->label(__('admin.fields.id')),
                TextInput::make('number')->disabled()->label(__('admin.fields.order_no')),
                Tabs::make(__('admin.tabs.translations'))->tabs([
                    Tabs\Tab::make(__('admin.tabs.english'))
                        ->schema([
                            TextInput::make('title')->required()->maxLength(200)->label(__('admin.fields.title')),
                            TextInput::make('short_title')->maxLength(100)->label(__('admin.fields.short_title')),
                            Textarea::make('description')->required()->maxLength(3000)->columnSpanFull()->label(__('admin.fields.description')),
                        ]),
                    Tabs\Tab::make(__('admin.tabs.russian'))
                        ->schema([
                            TextInput::make('ru.title')->maxLength(200)->label(__('admin.tabs.title_ru')),
                            TextInput::make('ru.shortTitle')->maxLength(100)->label(__('admin.tabs.short_ru')),
                            Textarea::make('ru.description')->maxLength(3000)->columnSpanFull()->label(__('admin.tabs.desc_ru')),
                        ])
                        ->badge(fn ($record): string => !empty($record?->ru['title']) ? __('admin.tabs.translated') : __('admin.tabs.empty')),
                    Tabs\Tab::make(__('admin.tabs.kyrgyz'))
                        ->schema([
                            TextInput::make('ky.title')->maxLength(200)->label(__('admin.tabs.title_ky')),
                            TextInput::make('ky.shortTitle')->maxLength(100)->label(__('admin.tabs.short_ky')),
                            Textarea::make('ky.description')->maxLength(3000)->columnSpanFull()->label(__('admin.tabs.desc_ky')),
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
                    // Translation badges live in one row under the title.
                    Split::make([
                        TextColumn::make('ru')
                            ->getStateUsing(fn ($record): string => !empty($record->ru['title']) ? '✓ ru' : '— ru')
                            ->color(fn ($record): string => !empty($record->ru['title']) ? 'success' : 'gray')
                            ->badge(),
                        TextColumn::make('ky')
                            ->getStateUsing(fn ($record): string => !empty($record->ky['title']) ? '✓ ky' : '— ky')
                            ->color(fn ($record): string => !empty($record->ky['title']) ? 'success' : 'gray')
                            ->badge(),
                    ]),
                    TextColumn::make('number')->label('#')->sortable(),
                    TextColumn::make('short_title')->label(__('admin.fields.short'))->placeholder('—'),
                    TextColumn::make('services_count')->counts('services')->label(__('admin.fields.services')),
                ]),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('open')
                        ->label(__('admin.actions.open_on_site'))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (Category $record): string => url('/services/'.$record->id))
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
            ->defaultSort('number');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
