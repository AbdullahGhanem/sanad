<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consent_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('anonymous_id')->index();
            $table->string('version');
            $table->string('locale', 8)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('agreed_at');
            $table->timestamps();

            $table->index(['anonymous_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_records');
    }
};
