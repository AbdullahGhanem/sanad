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
        Schema::table('crisis_events', function (Blueprint $table) {
            $table->string('status')->default('open')->index()->after('severity');
            $table->foreignId('handled_by_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable()->after('handled_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crisis_events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('handled_by_id');
            $table->dropColumn(['status', 'handled_at']);
        });
    }
};
