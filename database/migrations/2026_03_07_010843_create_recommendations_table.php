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
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('body_ar');
            $table->text('body_en');
            $table->string('url')->nullable();
            $table->unsignedInteger('min_phq9')->default(0);
            $table->unsignedInteger('max_phq9')->default(27);
            $table->unsignedInteger('min_gad7')->default(0);
            $table->unsignedInteger('max_gad7')->default(21);
            $table->string('language')->default('both');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
