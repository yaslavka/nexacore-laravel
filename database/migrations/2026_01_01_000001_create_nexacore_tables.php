<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedInteger('number')->default(0);
            $table->string('title');
            $table->string('short_title')->nullable();
            $table->text('description');
            $table->string('icon')->nullable();
            $table->json('ru')->nullable();
            $table->json('ky')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('category_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('title');
            $table->text('description');
            $table->string('duration')->nullable();
            $table->string('period')->default('');
            $table->decimal('price', 12, 2)->nullable();
            $table->string('price_label')->nullable();
            $table->json('packages');
            $table->json('deliverables');
            $table->json('ru')->nullable();
            $table->json('ky')->nullable();
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->unique();
            $table->string('service_id');
            $table->string('service_title');
            $table->string('package_name')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('company')->nullable();
            $table->text('message')->nullable();
            $table->boolean('consent')->default(true);
            $table->timestamp('consent_at')->nullable();
            $table->string('status')->default('new');
            $table->string('payment_status')->default('not_connected');
            $table->string('notification_status')->default('not_connected');
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->uuid('order_id');
            $table->unsignedBigInteger('amount');
            $table->string('currency', 8)->default('USD');
            $table->string('status')->default('created');
            $table->string('mode')->default('off');
            $table->boolean('session_requested')->default(false);
            $table->string('provider_status')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_start_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('tagline', 500)->default('');
            $table->string('email');
            $table->string('phone');
            $table->string('address', 1000);
            $table->string('legal_entity', 1000);
            $table->text('privacy_text')->nullable();
            $table->text('terms_text')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('at');
            $table->string('actor')->default('public');
            $table->string('action');
            $table->string('target')->default('');
            $table->timestamp('created_at')->nullable();
            $table->index('at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('viewer')->after('password');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('services');
        Schema::dropIfExists('categories');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
