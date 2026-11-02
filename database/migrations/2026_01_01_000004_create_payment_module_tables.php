<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Payment module storage:
 *  - payment_providers: payment systems (Payler, demo, …) registered as modules;
 *  - payment_merchants: merchant accounts of a payment system, one per theme;
 *  - customers: public users auto-created by the charge API (auto password);
 *  - payments: extended with provider/merchant/theme/session columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_providers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name', 120);
            $table->string('driver', 60);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('payment_merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_provider_id')->constrained('payment_providers')->cascadeOnDelete();
            $table->string('theme_slug', 60)->default('*');
            $table->string('name', 160);
            $table->string('host', 160)->default('secure.payler.com');
            $table->text('terminal_key')->nullable();
            $table->text('terminal_secret')->nullable();
            $table->string('mode', 12)->default('live');
            $table->string('currency', 3)->default('KGS');
            $table->string('operation_type', 12)->default('OneStep');
            $table->unsignedInteger('lifetime_minutes')->default(60);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['payment_provider_id', 'theme_slug'], 'payment_merchants_provider_theme_unique');
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email', 254)->unique();
            $table->string('name', 120)->default('');
            $table->string('phone', 40)->nullable();
            $table->string('password');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('payment_provider_id')->nullable()->after('id')->constrained('payment_providers')->nullOnDelete();
            $table->foreignId('payment_merchant_id')->nullable()->after('payment_provider_id')->constrained('payment_merchants')->nullOnDelete();
            $table->uuid('customer_id')->nullable()->after('payment_merchant_id');
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->string('theme_slug', 60)->nullable()->after('customer_id');
            $table->string('session_id', 120)->nullable()->after('provider_status');
            $table->string('provider_order_id', 120)->nullable()->after('session_id')->index();
            $table->string('payment_url', 1000)->nullable()->after('provider_order_id');
        });

        // Seed the Payler module as the first payment system (idempotent).
        Schema::hasTable('payment_providers') && \Illuminate\Support\Facades\DB::table('payment_providers')->updateOrInsert(
            ['code' => 'payler'],
            ['name' => 'Payler', 'driver' => 'payler', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
        );
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            foreach (['payment_provider_id', 'payment_merchant_id', 'customer_id'] as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    try {
                        $table->dropForeign([$column]);
                    } catch (\Throwable) {
                    }
                    $table->dropColumn($column);
                }
            }
            foreach (['theme_slug', 'session_id', 'provider_order_id', 'payment_url'] as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('customers');
        Schema::dropIfExists('payment_merchants');
        Schema::dropIfExists('payment_providers');
    }
};
