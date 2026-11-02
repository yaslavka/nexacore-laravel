<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use App\Models\User;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Directions';

    protected static ?string $pluralModelLabel = 'Directions';

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
                TextInput::make('id')->disabled()->label('ID'),
                TextInput::make('number')->disabled()->label('Order No.'),
                Tabs::make('Translations')->tabs([
                    Tabs\Tab::make('English (canonical)')
                        ->schema([
                            TextInput::make('title')->required()->maxLength(200),
                            TextInput::make('short_title')->maxLength(100)->label('Short title'),
                            Textarea::make('description')->required()->maxLength(3000)->columnSpanFull(),
                        ]),
                    Tabs\Tab::make('Russian')
                        ->schema([
                            TextInput::make('ru.title')->maxLength(200)->label('Title (ru)'),
                            TextInput::make('ru.shortTitle')->maxLength(100)->label('Short title (ru)'),
                            Textarea::make('ru.description')->maxLength(3000)->columnSpanFull()->label('Description (ru)'),
                        ])
                        ->badge(fn ($record): string => !empty($record?->ru['title']) ? 'translated' : 'empty'),
                    Tabs\Tab::make('Kyrgyz')
                        ->schema([
                            TextInput::make('ky.title')->maxLength(200)->label('Title (ky)'),
                            TextInput::make('ky.shortTitle')->maxLength(100)->label('Short title (ky)'),
                            Textarea::make('ky.description')->maxLength(3000)->columnSpanFull()->label('Description (ky)'),
                        ])
                        ->badge(fn ($record): string => !empty($record?->ky['title']) ? 'translated' : 'empty'),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label('#')->sortable(),
                TextColumn::make('title')->limit(40)->searchable(),
                TextColumn::make('short_title')->label('Short')->placeholder('—'),
                TextColumn::make('services_count')->counts('services')->label('Services'),
                TextColumn::make('ru')
                    ->formatStateUsing(fn ($record): string => !empty($record->ru['title']) ? '✓ ru' : '— ru')
                    ->color(fn ($record): string => !empty($record->ru['title']) ? 'success' : 'gray')
                    ->badge(),
                TextColumn::make('ky')
                    ->formatStateUsing(fn ($record): string => !empty($record->ky['title']) ? '✓ ky' : '— ky')
                    ->color(fn ($record): string => !empty($record->ky['title']) ? 'success' : 'gray')
                    ->badge(),
            ])
            ->actions([
                EditAction::make(),
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
