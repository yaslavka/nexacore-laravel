<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Public customer created automatically by the payment charge API.
 * Passwords are generated automatically and sent to the customer once.
 * Deliberately separate from the admin `users` table.
 */
class Customer extends Authenticatable
{
    use HasUuids;
    use Notifiable;

    protected $fillable = [
        'email', 'name', 'phone', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
