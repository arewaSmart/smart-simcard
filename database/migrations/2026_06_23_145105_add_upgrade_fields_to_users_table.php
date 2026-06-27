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
            $table->string('pending_role')->nullable()->after('role');
            $table->enum('upgrade_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('pending_role');
            $table->timestamp('upgrade_requested_at')->nullable()->after('upgrade_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pending_role', 'upgrade_status', 'upgrade_requested_at']);
        });
    }
};
