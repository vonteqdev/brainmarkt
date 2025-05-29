<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id(); // Uses auto-incrementing BIGINT by default
            $table->string('name', 100)->unique(); // e.g., manage_clients, view_reports
            $table->string('display_name', 255)->nullable();
            $table->string('module', 50)->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
