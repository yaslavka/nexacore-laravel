<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'id', 'reference', 'service_id', 'service_title', 'package_name',
        'name', 'email', 'company', 'message', 'consent', 'consent_at',
        'status', 'payment_status', 'notification_status',
    ];

    protected $casts = [
        'consent' => 'boolean',
        'consent_at' => 'datetime',
    ];

    public const STATUSES = ['new', 'in_progress', 'completed', 'cancelled'];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id');
    }
}
