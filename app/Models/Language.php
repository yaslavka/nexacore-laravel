<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Language extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code', 'name', 'native_name', 'is_active', 'is_default', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /** Active languages ordered for the site and the admin switcher. */
    public static function active()
    {
        return Cache::remember('site.languages.active', now()->addMinutes(10), function () {
            $languages = self::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('code')
                ->get();

            // Table not seeded yet — fall back to the original trilingual set.
            if ($languages->isEmpty()) {
                $fallback = [
                    ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'is_active' => true, 'is_default' => true, 'sort_order' => 1],
                    ['code' => 'ru', 'name' => 'Russian', 'native_name' => 'Русский', 'is_active' => true, 'is_default' => false, 'sort_order' => 2],
                    ['code' => 'ky', 'name' => 'Kyrgyz', 'native_name' => 'Кыргызча', 'is_active' => true, 'is_default' => false, 'sort_order' => 3],
                ];

                $languages = collect($fallback)->map(fn (array $row) => new self($row));
            }

            return $languages;
        });
    }

    public static function defaultCode(): string
    {
        return self::active()->firstWhere('is_default', true)?->code
            ?? self::active()->first()?->code
            ?? 'en';
    }

    public static function forgetCache(): void
    {
        Cache::forget('site.languages.active');
    }
}
