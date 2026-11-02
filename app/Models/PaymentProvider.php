<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentProvider extends Model
{
    protected $fillable = [
        'code', 'name', 'driver', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function merchants()
    {
        return $this->hasMany(PaymentMerchant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
