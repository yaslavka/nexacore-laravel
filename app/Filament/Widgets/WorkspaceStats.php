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
            Stat::make(__('admin.stats.new_enquiries'), Order::query()->where('status', 'new')->count())
                ->description(__('admin.stats.new_enquiries_desc'))
                ->color('info')
                ->icon('heroicon-o-inbox-arrow-down'),
            Stat::make(__('admin.stats.in_progress'), Order::query()->where('status', 'in_progress')->count())
                ->color('warning')
                ->icon('heroicon-o-arrow-path'),
            Stat::make(__('admin.stats.completed'), Order::query()->where('status', 'completed')->count())
                ->color('success')
                ->icon('heroicon-o-check-circle'),
            Stat::make(__('admin.stats.payments'), Payment::query()->where('status', '!=', 'cancelled')->count())
                ->description(__('admin.stats.payments_desc'))
                ->color('gray')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
