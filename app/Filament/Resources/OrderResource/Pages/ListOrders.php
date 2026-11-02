<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Services\CatalogService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportCsv')
                ->label(__('admin.order_actions.export_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn (): bool => auth()->user()?->canExport() ?? false)
                ->action(fn () => $this->exportCsv()),
            Actions\Action::make('backup')
                ->label(__('admin.order_actions.backup_json'))
                ->icon('heroicon-o-archive-box')
                ->visible(fn (): bool => auth()->user()?->canExport() ?? false)
                ->action(function () {
                    $catalog = app(CatalogService::class)->get('en');
                    $payload = [
                        'version' => 1,
                        'exportedAt' => now()->toISOString(),
                        'orders' => Order::query()->orderByDesc('created_at')->get()->toArray(),
                        'settings' => \App\Models\Setting::current()->toArray(),
                        'catalog' => $catalog,
                    ];
                    $name = 'nexacore-backup-'.now()->format('Ymd-His').'.json';

                    return response()->streamDownload(function () use ($payload) {
                        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    }, $name, ['Content-Type' => 'application/json']);
                }),
        ];
    }

    private function exportCsv(): StreamedResponse
    {
        $keys = ['reference', 'createdAt', 'serviceTitle', 'packageName', 'name', 'email', 'company', 'message', 'status', 'paymentStatus'];

        return response()->streamDownload(function () use ($keys) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $keys, ',', '"', '\\');
            Order::query()->orderByDesc('created_at')->chunk(500, function ($orders) use ($out, $keys) {
                foreach ($orders as $order) {
                    $row = [
                        'reference' => $order->reference,
                        'createdAt' => $order->created_at?->toISOString(),
                        'serviceTitle' => $order->service_title,
                        'packageName' => $order->package_name,
                        'name' => $order->name,
                        'email' => $order->email,
                        'company' => $order->company,
                        'message' => $order->message,
                        'status' => $order->status,
                        'paymentStatus' => $order->payment_status,
                    ];
                    fputcsv($out, array_values($row), ',', '"', '\\');
                }
            });
            fclose($out);
        }, 'nexacore-orders.csv', ['Content-Type' => 'text/csv; charset=utf-8']);
    }
}
