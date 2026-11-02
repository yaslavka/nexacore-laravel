<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditEventResource\Pages;
use App\Models\AuditEvent;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Table;

class AuditEventResource extends Resource
{
    protected static ?string $model = AuditEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.audit.nav');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.audit.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.audit.plural');
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('action')
                        ->label(__('admin.fields.action'))
                        ->badge()
                        ->color(fn (string $state): string => str_contains($state, 'cancel') ? 'danger' : (str_contains($state, 'paid') ? 'success' : 'info')),
                    TextColumn::make('at')
                        ->label(__('admin.fields.time'))
                        ->dateTime('d M Y, H:i:s')
                        ->sortable(),
                    TextColumn::make('actor')
                        ->label(__('admin.fields.actor'))
                        ->badge()
                        ->color('gray'),
                    TextColumn::make('target')
                        ->label(__('admin.fields.target'))
                        ->limit(40)
                        ->copyable()
                        ->placeholder('—'),
                ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'xl' => 3,
            ])
            ->paginated([10, 25, 50])
            ->defaultSort('at', 'desc')
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditEvents::route('/'),
        ];
    }
}
