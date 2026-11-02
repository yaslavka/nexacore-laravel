<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A merchant account of a payment system, bound to a site theme.
 * A payment system can have an unlimited number of merchants; the merchant
 * serving a payment is chosen by the theme it is linked to.
 */
class PaymentMerchant extends Model
{
    public const ANY_THEME = '*';

    protected $fillable = [
        'payment_provider_id', 'theme_slug', 'name', 'host',
        'terminal_key', 'terminal_secret', 'mode', 'currency',
        'operation_type', 'lifetime_minutes', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'terminal_key' => 'encrypted',
        'terminal_secret' => 'encrypted',
        'lifetime_minutes' => 'integer',
    ];

    public function provider()
    {
        return $this->belongsTo(PaymentProvider::class, 'payment_provider_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isSandbox(): bool
    {
        return $this->mode === 'test';
    }

    public function callbackUrl(): string
    {
        $driver = app(\App\Modules\Payments\PaymentModuleManager::class)
            ->forProvider($this->provider);

        return method_exists($driver, 'callbackUrl') ? $driver->callbackUrl() : url('/api/payments/'.$this->provider->code.'/callback');
    }
}
