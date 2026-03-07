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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student')->after('password');
            $table->unsignedBigInteger('faculty_id')->nullable()->after('role');
            $table->boolean('reminder_enabled')->default(true)->after('faculty_id');
            $table->timestamp('last_screened_at')->nullable()->after('reminder_enabled');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'faculty_id', 'reminder_enabled', 'last_screened_at']);
            $table->dropSoftDeletes();
        });
    }
};
