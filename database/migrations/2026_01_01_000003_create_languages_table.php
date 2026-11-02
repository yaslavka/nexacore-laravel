<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->string('code', 12)->primary();
            $table->string('name', 100);
            $table->string('native_name', 100);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed the original trilingual set (idempotent).
        foreach ([
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'is_active' => true, 'is_default' => true, 'sort_order' => 1],
            ['code' => 'ru', 'name' => 'Russian', 'native_name' => 'Русский', 'is_active' => true, 'is_default' => false, 'sort_order' => 2],
            ['code' => 'ky', 'name' => 'Kyrgyz', 'native_name' => 'Кыргызча', 'is_active' => true, 'is_default' => false, 'sort_order' => 3],
        ] as $language) {
            Schema::hasTable('languages') && \Illuminate\Support\Facades\DB::table('languages')->updateOrInsert(
                ['code' => $language['code']],
                $language + ['created_at' => now(), 'updated_at' => now()],
            );
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('locale', 12)->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
