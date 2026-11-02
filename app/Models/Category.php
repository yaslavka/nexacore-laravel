<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'number', 'title', 'short_title', 'description', 'icon', 'ru', 'ky'];

    protected $casts = [
        'ru' => 'array',
        'ky' => 'array',
        'number' => 'integer',
    ];

    public function services()
    {
        return $this->hasMany(Service::class, 'category_id')->orderBy('sort_order');
    }

    public function translation(?string $locale): ?array
    {
        return match ($locale) {
            'ru' => $this->ru,
            'ky' => $this->ky,
            default => null,
        };
    }
}
