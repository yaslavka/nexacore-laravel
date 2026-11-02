<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLES = ['owner', 'editor', 'viewer'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, self::ROLES, true);
    }

    public function canEditCatalog(): bool
    {
        return $this->role !== 'viewer';
    }

    public function canEditOrders(): bool
    {
        return $this->role !== 'viewer';
    }

    public function canManageSettings(): bool
    {
        return $this->role === 'owner';
    }

    public function canExport(): bool
    {
        return $this->role === 'owner';
    }
}
