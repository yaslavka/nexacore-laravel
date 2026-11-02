<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Multi-company workspace. A company is a legal entity whose details are
 * shown on the public website, in invoices and in GET /api/settings. There
 * can be any number of companies; exactly one of them is `is_default` —
 * the one displayed publicly (see App\Models\Company::booted()).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('legal_entity', 1000)->default('');
            $table->string('tagline', 500)->default('');
            $table->string('email', 254)->default('');
            $table->string('phone', 100)->default('');
            $table->string('address', 1000)->default('');
            $table->text('privacy_text')->nullable();
            $table->text('terms_text')->nullable();
            $table->boolean('is_default')->default(false)->index();
            $table->timestamps();
        });

        // Carry the previously configured single company over, so the
        // public site keeps showing exactly the same details after upgrade.
        $settings = Schema::hasTable('settings')
            ? DB::table('settings')->find(1)
            : null;

        DB::table('companies')->insert([
            'name' => $settings->company_name ?? 'NexaCore LLC',
            'legal_entity' => $settings->legal_entity ?? 'ОсОО «НексаКор» / NexaCore LLC',
            'tagline' => $settings->tagline ?? '',
            'email' => $settings->email ?? 'office@nexa-core-technologies.com',
            'phone' => $settings->phone ?? '+996 501 750 774',
            'address' => $settings->address ?? 'Кыргызская Республика, г. Бишкек, Первомайский район, проспект Чынгыза Айтматова, дом 126, квартира 68',
            'privacy_text' => $settings->privacy_text ?? null,
            'terms_text' => $settings->terms_text ?? null,
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
