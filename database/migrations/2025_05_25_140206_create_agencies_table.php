<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('contact_person_name')->nullable();
            $table->string('contact_email')->unique();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('currency_preference', 10)->default('USD');
            // For future subscription management
            // $table->foreignUuid('subscription_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            // $table->string('subscription_status', 50)->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
