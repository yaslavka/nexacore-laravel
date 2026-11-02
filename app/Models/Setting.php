<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Setting extends Model
{
    protected $fillable = [
        'company_name', 'tagline', 'email', 'phone', 'address',
        'legal_entity', 'privacy_text', 'terms_text', 'active_theme',
    ];

    /**
     * These columns are NOT NULL in the schema. An empty form field
     * arrives as null (Livewire), so coerce it to an empty string
     * instead of tripping the SQLite integrity constraint.
     */
    protected array $notNullStrings = [
        'company_name', 'tagline', 'email', 'phone', 'address', 'legal_entity',
    ];

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->notNullStrings, true)) {
            $value = (string) ($value ?? '');
        }

        return parent::setAttribute($key, $value);
    }

    public static function defaults(): array
    {
        return [
            'company_name' => 'NexaCore LLC',
            'tagline' => '',
            'email' => 'office@nexa-core-technologies.com',
            'phone' => '+996 501 750 774',
            'address' => 'Кыргызская Республика, г. Бишкек, Первомайский район, проспект Чынгыза Айтматова, дом 126, квартира 68',
            'legal_entity' => 'ОсОО «НексаКор» / NexaCore LLC',
            'privacy_text' => '',
            'terms_text' => '',
        ];
    }

    public static function current(): self
    {
        $model = self::query()->find(1);
        if ($model) {
            foreach (['company_name', 'email', 'phone', 'address', 'legal_entity'] as $key) {
                if (!trim((string) $model->{$key})) {
                    $model->{$key} = self::defaults()[$key];
                }
            }

            return $model;
        }

        return new self(array_merge(self::defaults(), ['id' => 1]));
    }

    public static function saveCurrent(array $attributes): self
    {
        return DB::transaction(function () use ($attributes) {
            $model = self::current();
            $model->fill($attributes);
            $model->id = $model->id ?: 1;
            $model->save();

            return $model;
        });
    }
}
