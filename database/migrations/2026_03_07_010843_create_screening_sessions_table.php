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
        Schema::create('screening_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('anonymous_id');
            $table->unsignedInteger('phq9_score')->nullable();
            $table->unsignedInteger('gad7_score')->nullable();
            $table->string('combined_severity')->nullable();
            $table->string('nlp_classification')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('anonymous_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screening_sessions');
    }
};
