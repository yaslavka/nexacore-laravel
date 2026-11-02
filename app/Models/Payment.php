<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'order_id', 'amount', 'currency', 'status', 'mode',
        'session_requested', 'provider_status', 'payload',
        'expires_at', 'last_start_at', 'paid_at',
        'payment_provider_id', 'payment_merchant_id', 'customer_id',
        'theme_slug', 'session_id', 'provider_order_id', 'payment_url',
    ];

    protected $casts = [
        'session_requested' => 'boolean',
        'payload' => 'array',
        'expires_at' => 'datetime',
        'last_start_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'integer',
    ];

    public const TERMINAL = ['completed', 'refunded', 'reversed', 'cancelled', 'expired'];

    /** Final, successful state. */
    public const STATUS_PAID = 'paid';

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function provider()
    {
        return $this->belongsTo(PaymentProvider::class, 'payment_provider_id');
    }

    public function merchant()
    {
        return $this->belongsTo(PaymentMerchant::class, 'payment_merchant_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, self::TERMINAL, true);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast() && $this->status !== self::STATUS_PAID;
    }
}
