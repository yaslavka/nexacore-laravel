<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AuditEvent extends Model
{
    use HasUuids;

    protected $table = 'events';

    public $timestamps = false;

    protected $fillable = ['id', 'at', 'actor', 'action', 'target'];

    protected $casts = ['at' => 'datetime'];

    public static function record(string $action, string $target = '', string $actor = 'public'): void
    {
        self::query()->create([
            'at' => now(),
            'actor' => $actor,
            'action' => $action,
            'target' => $target,
        ]);
    }
}
