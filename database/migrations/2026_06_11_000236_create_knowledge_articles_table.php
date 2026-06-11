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
        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('title_en');
            $table->string('title_ar');
            $table->text('body_en');
            $table->text('body_ar');
            $table->json('embedding_en')->nullable();
            $table->json('embedding_ar')->nullable();
            $table->string('embedding_model')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_articles');
    }
};
