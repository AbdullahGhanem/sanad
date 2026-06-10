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
        Schema::create('coping_exercises', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->json('themes');
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('summary_en')->nullable();
            $table->string('summary_ar')->nullable();
            $table->text('steps_en');
            $table->text('steps_ar');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coping_exercises');
    }
};
