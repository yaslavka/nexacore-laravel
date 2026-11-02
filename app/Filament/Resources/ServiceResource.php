<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\AuditEvent;
use App\Models\Service;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Services';

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
                    TextInput::make('id')->disabled()->label('ID'),
                    TextInput::make('category_id')->disabled()->label('Category'),
                    TextInput::make('duration')->maxLength(200)->label('Duration (en)'),
                ]),
                Tabs::make('Translations')->tabs([
                    Tabs\Tab::make('English (canonical)')
                        ->schema([
                            TextInput::make('title')->required()->maxLength(200)->columnSpanFull(),
                            Textarea::make('description')->required()->maxLength(3000)->columnSpanFull(),
                            TextInput::make('price')
                                ->numeric()
                                ->minValue(0)
                                ->nullable()
                                ->prefix('$')
                                ->label('Price from (USD)')
                                ->helperText('Leave empty for a custom quote. Prices are indicative.'),
                            TagsInput::make('deliverables')
                                ->suggestions(['Page structure', 'Responsive pages', 'Contact form'])
                                ->columnSpanFull(),
                            Repeater::make('packages')
                                ->schema([
                                    TextInput::make('name')->required()->maxLength(160),
                                    Textarea::make('description')->required()->maxLength(3000)->columnSpan(2),
                                    TextInput::make('price')->numeric()->minValue(0)->nullable()->prefix('$'),
                                ])
                                ->columns(4)
                                ->columnSpanFull(),
                        ]),
                    Tabs\Tab::make('Russian')
                        ->schema([
                            TextInput::make('ru.title')->maxLength(200)->label('Title (ru)'),
                            Textarea::make('ru.description')->maxLength(3000)->columnSpanFull()->label('Description (ru)'),
                            TextInput::make('ru.duration')->maxLength(200)->label('Duration (ru)'),
                        ])
                        ->badge(fn ($record): string => !empty($record?->ru['title']) ? 'translated' : 'empty'),
                    Tabs\Tab::make('Kyrgyz')
                        ->schema([
                            TextInput::make('ky.title')->maxLength(200)->label('Title (ky)'),
                            Textarea::make('ky.description')->maxLength(3000)->columnSpanFull()->label('Description (ky)'),
                            TextInput::make('ky.duration')->maxLength(200)->label('Duration (ky)'),
                        ])
                        ->badge(fn ($record): string => !empty($record?->ky['title']) ? 'translated' : 'empty'),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->limit(12),
                TextColumn::make('title')->limit(40)->searchable(),
                TextColumn::make('category_id')->label('Category'),
                TextColumn::make('price')
                    ->formatStateUsing(fn ($state): string => $state === null ? 'Custom quote' : '$'.number_format((float) $state))
                    ->alignEnd(),
                TextColumn::make('ru')
                    ->formatStateUsing(fn ($record): string => !empty($record->ru['title']) ? '✓ ru' : '— ru')
                    ->badge()
                    ->color(fn ($record): string => !empty($record->ru['title']) ? 'success' : 'gray'),
                TextColumn::make('ky')
                    ->formatStateUsing(fn ($record): string => !empty($record->ky['title']) ? '✓ ky' : '— ky')
                    ->badge()
                    ->color(fn ($record): string => !empty($record->ky['title']) ? 'success' : 'gray'),
            ])
            ->actions([
                EditAction::make(),
            ])
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
