<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\AuditEvent;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMerchant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    /**
     * The payment link is generated for a PAYMENT SYSTEM, not for an
     * existing order: the admin picks any active merchant of any provider
     * and the enquiry (order) row is created automatically here. Existing
     * client orders are never reused or modified.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('payment_merchant_id')
                    ->label(__('admin.fields.payment_system'))
                    ->options($this->merchantOptions())
                    ->searchable()
                    ->required()
                    ->helperText(__('admin.fields.payment_system_helper')),
                TextInput::make('amount_major')
                    ->label(__('admin.fields.amount'))
                    ->numeric()
                    ->minValue(0.01)
                    ->required()
                    ->helperText(__('admin.fields.amount_helper')),
                TextInput::make('product')
                    ->label(__('admin.fields.payment_purpose'))
                    ->maxLength(200)
                    ->placeholder(__('admin.fields.payment_purpose_default'))
                    ->helperText(__('admin.fields.payment_purpose_helper')),
                TextInput::make('email')
                    ->label(__('admin.fields.email'))
                    ->email()
                    ->required(),
            ])
            ->columns(2);
    }

    /**
     * Every active merchant of every active payment system, grouped by
     * the payment system name — the theme binding does not matter here:
     * the admin explicitly chooses whom to invoice through.
     */
    protected function merchantOptions(): array
    {
        return PaymentMerchant::query()->active()
            ->whereHas('provider', fn ($query) => $query->active())
            ->with('provider')
            ->get()
            ->groupBy(fn (PaymentMerchant $merchant) => $merchant->provider->name)
            ->mapWithKeys(fn ($merchants, $providerName) => [
                $providerName => $merchants
                    ->mapWithKeys(fn (PaymentMerchant $merchant) => [
                        $merchant->id => sprintf(
                            '%s · %s%s%s',
                            $merchant->name,
                            strtoupper($merchant->currency ?: (string) config('payments.default_currency', 'USD')),
                            $merchant->theme_slug !== PaymentMerchant::ANY_THEME ? ' · '.$merchant->theme_slug : '',
                            $merchant->isSandbox() ? ' · TEST' : '',
                        ),
                    ])->all(),
            ])
            ->all();
    }

    protected function handleRecordCreation(array $data): Model
    {
        $merchant = PaymentMerchant::query()->active()
            ->whereHas('provider', fn ($query) => $query->active())
            ->with('provider')
            ->find($data['payment_merchant_id'] ?? null);

        if (! $merchant) {
            Notification::make()
                ->title(__('admin.notifications.payment_system_unavailable'))
                ->danger()
                ->send();
            throw new Halt;
        }

        $amountMinor = (int) round(((float) $data['amount_major']) * 100);
        $product = mb_substr(
            trim((string) ($data['product'] ?? '')) ?: __('admin.fields.payment_purpose_default'),
            0,
            200,
        );
        $email = strtolower(trim((string) $data['email']));
        $lifetime = max(1, (int) ($merchant->lifetime_minutes ?: config('payments.payler.lifetime_minutes', 60)));

        // Order + payment rows are created atomically; the gateway session
        // is NOT opened here — it starts when the client proceeds to pay
        // from the checkout page, so the session lifetime is not wasted.
        return DB::transaction(function () use ($merchant, $amountMinor, $product, $email, $lifetime): Payment {
            $order = Order::query()->create([
                'id' => (string) Str::uuid(),
                'reference' => sprintf('MANUAL-%s', strtoupper(bin2hex(random_bytes(4)))),
                'service_id' => 'manual',
                'service_title' => $product,
                'package_name' => null,
                'name' => $email,
                'email' => $email,
                'company' => null,
                'message' => __('admin.notifications.manual_order_message', ['system' => $merchant->provider->name]),
                'consent' => true,
                'consent_at' => now(),
                'status' => 'new',
                'payment_status' => 'pending',
            ]);

            return Payment::query()->create([
                'id' => bin2hex(random_bytes(32)),
                'order_id' => $order->id,
                'payment_provider_id' => $merchant->payment_provider_id,
                'payment_merchant_id' => $merchant->id,
                'customer_id' => null,
                'theme_slug' => \theme()->slug,
                'amount' => $amountMinor,
                'currency' => strtoupper($merchant->currency ?: (string) config('payments.default_currency', 'USD')),
                'status' => 'created',
                'mode' => $merchant->isSandbox() ? 'sandbox' : 'live',
                'session_requested' => false,
                'payload' => [
                    'product' => $product,
                    'email' => $email,
                    'lang' => 'ru',
                    'source' => 'admin',
                ],
                'expires_at' => now()->addMinutes($lifetime),
            ]);
        });
    }

    protected function afterCreate(): void
    {
        AuditEvent::record('payment.created', $this->record->id, auth()->user()?->email ?? 'system');

        Notification::make()
            ->title(__('admin.notifications.payment_link_ready'))
            ->body(__('admin.notifications.payment_link_ready_body'))
            ->success()
            ->actions([
                NotificationAction::make('open_checkout')
                    ->label(__('admin.actions.open_payment_page'))
                    ->url(url('/checkout/'.$this->record->id))
                    ->openUrlInNewTab(),
            ])
            ->persistent()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return PaymentResource::getUrl('view', ['record' => $this->record]);
    }
}
