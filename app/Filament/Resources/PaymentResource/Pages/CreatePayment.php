<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\AuditEvent;
use App\Models\Order;
use App\Models\Payment;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function beforeCreate(): void
    {
        $orderId = $this->data['order_id'] ?? null;
        $order = Order::query()->find($orderId);

        if (! $order) {
            Notification::make()->title('Enquiry not found')->danger()->send();
            $this->halt();
        }

        if ($order->status === 'cancelled') {
            Notification::make()->title('Cannot invoice a cancelled enquiry')->danger()->send();
            $this->halt();
        }

        $existing = Payment::query()
            ->where('order_id', $order->id)
            ->where('status', '!=', 'cancelled')
            ->first();
        if ($existing) {
            Notification::make()->title('This enquiry already has a payment link')->danger()->send();
            $this->halt();
        }

        $amountMajor = (float) ($this->data['amount_major'] ?? 0);
        $amountMinor = (int) round($amountMajor * 100);

        // Currency and mode follow the merchant bound to the active theme
        // (the gateway session is opened through that merchant later).
        $merchant = app(\App\Services\PaymentService::class)
            ->resolveMerchant(null, \theme()->slug);

        $this->record = new Payment([
            'id' => bin2hex(random_bytes(32)),
            'order_id' => $order->id,
            'amount' => $amountMinor,
            'currency' => $merchant?->currency ?: config('payments.default_currency', 'USD'),
            'status' => 'created',
            'mode' => $merchant ? ($merchant->isSandbox() ? 'sandbox' : 'live') : 'off',
            'session_requested' => false,
            'payload' => [
                'product' => Str::limit($order->service_title, 256),
                'email' => $order->email,
            ],
            'expires_at' => now()->addDay(),
        ]);

        $order->payment_status = 'awaiting_payment';
        $order->save();

        AuditEvent::record('payment.created', $this->record->id, auth()->user()?->email ?? 'system');
    }

    protected function getRedirectUrl(): string
    {
        return PaymentResource::getUrl('index');
    }
}
