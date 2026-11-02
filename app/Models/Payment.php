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

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
