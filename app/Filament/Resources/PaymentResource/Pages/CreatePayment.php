<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\AuditEvent;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Str;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    /**
     * Filament 3 builds AND saves the row from the form data itself
     * (handleRecordCreation: `new Payment($data)` → `save()`), while
     * beforeCreate() runs before $this->record even exists — anything
     * assigned to $this->record there is discarded. Every column the
     * row needs must be injected into $data right here.
     */
    public function mutateFormDataBeforeCreate(array $data): array
    {
        $order = Order::query()->find($data['order_id'] ?? null);

        if (! $order) {
            Notification::make()->title('Enquiry not found')->danger()->send();
            throw new Halt;
        }

        if ($order->status === 'cancelled') {
            Notification::make()->title('Cannot invoice a cancelled enquiry')->danger()->send();
            throw new Halt;
        }

        $existing = Payment::query()
            ->where('order_id', $order->id)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existing) {
            Notification::make()->title('This enquiry already has a payment link')->danger()->send();
            throw new Halt;
        }

        $amountMajor = (float) ($data['amount_major'] ?? 0);

        // Currency and mode follow the merchant bound to the active theme
        // (the gateway session is opened through that merchant later).
        $merchant = app(PaymentService::class)->resolveMerchant(null, \theme()->slug);

        $data['id'] = bin2hex(random_bytes(32));
        $data['amount'] = (int) round($amountMajor * 100);
        $data['currency'] = $merchant?->currency ?: config('payments.default_currency', 'USD');
        $data['status'] = 'created';
        $data['mode'] = $merchant ? ($merchant->isSandbox() ? 'sandbox' : 'live') : 'off';
        $data['session_requested'] = false;
        $data['payload'] = [
            'product' => Str::limit($order->service_title, 256),
            'email' => $order->email,
        ];
        $data['expires_at'] = now()->addDay();

        return $data;
    }

    protected function afterCreate(): void
    {
        $order = $this->record->order;
        $order->payment_status = 'awaiting_payment';
        $order->save();

        AuditEvent::record('payment.created', $this->record->id, auth()->user()?->email ?? 'system');
    }

    protected function getRedirectUrl(): string
    {
        return PaymentResource::getUrl('index');
    }
}
