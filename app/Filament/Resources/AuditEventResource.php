<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditEventResource\Pages;
use App\Models\AuditEvent;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditEventResource extends Resource
{
    protected static ?string $model = AuditEvent::class;

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Audit log';

    protected static ?string $pluralModelLabel = 'Audit log';

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
                TextColumn::make('at')->label('Time')->dateTime('d M Y, H:i:s')->sortable(),
                TextColumn::make('actor')->badge()->color('gray'),
                TextColumn::make('action')->badge()->color(fn (string $state): string => str_contains($state, 'cancel') ? 'danger' : (str_contains($state, 'paid') ? 'success' : 'info')),
                TextColumn::make('target')->limit(40)->copyable()->placeholder('—'),
            ])
            ->actions([
                ViewAction::make(),
            ])
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
