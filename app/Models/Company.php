<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A legal company of the workspace. There can be any number of companies,
 * exactly one of them is «primary» (is_default) — its details are shown on
 * the public website, in invoices and are returned by GET /api/settings.
 *
 * The primary company is managed automatically:
 *   • saving a company with is_default = true clears the flag on the rest;
 *   • if no company is primary after a save, the saved one becomes primary;
 *   • after deleting the primary company the first remaining one takes over.
 */
class Company extends Model
{
    protected $fillable = [
        'name', 'legal_entity', 'tagline', 'email', 'phone', 'address',
        'privacy_text', 'terms_text', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * These columns are NOT NULL in the schema. An empty form field arrives
     * as null (Livewire), so coerce it to an empty string instead of
     * tripping the SQLite integrity constraint.
     */
    protected array $notNullStrings = [
        'name', 'legal_entity', 'tagline', 'email', 'phone', 'address',
    ];

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->notNullStrings, true)) {
            $value = (string) ($value ?? '');
        }

        return parent::setAttribute($key, $value);
    }

    protected static function booted(): void
    {
        // Exactly one company is always primary.
        static::saved(function (Company $company): void {
            if ($company->is_default) {
                Company::query()
                    ->whereKeyNot($company->getKey())
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            } elseif (! Company::query()->where('is_default', true)->exists()) {
                $company->forceFill(['is_default' => true])->saveQuietly();
            }
        });

        // After deleting the primary company the first remaining takes over.
        static::deleted(function (Company $company): void {
            if (! $company->is_default) {
                return;
            }

            Company::query()
                ->whereKeyNot($company->getKey())
                ->orderBy('id')
                ->first()
                ?->forceFill(['is_default' => true])->saveQuietly();
        });
    }

    /** The company shown on the public site (falls back to the first one). */
    public static function primary(): ?self
    {
        return self::query()
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first();
    }
}
