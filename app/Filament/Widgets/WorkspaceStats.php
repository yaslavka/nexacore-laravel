<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WorkspaceStats extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        return [
            Stat::make('New enquiries', Order::query()->where('status', 'new')->count())
                ->description('Awaiting first response')
                ->color('info')
                ->icon('heroicon-o-inbox-arrow-down'),
            Stat::make('In progress', Order::query()->where('status', 'in_progress')->count())
                ->color('warning')
                ->icon('heroicon-o-arrow-path'),
            Stat::make('Completed', Order::query()->where('status', 'completed')->count())
                ->color('success')
                ->icon('heroicon-o-check-circle'),
            Stat::make('Payments', Payment::query()->where('status', '!=', 'cancelled')->count())
                ->description('Active payment links')
                ->color('gray')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
