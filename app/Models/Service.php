<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'category_id', 'sort_order', 'title', 'description', 'duration', 'period',
        'price', 'price_label', 'packages', 'deliverables', 'ru', 'ky',
    ];

    protected $casts = [
        'packages' => 'array',
        'deliverables' => 'array',
        'ru' => 'array',
        'ky' => 'array',
        'price' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
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
