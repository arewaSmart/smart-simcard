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
        Schema::create('sims', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique()->index();
            $table->string('category')->index(); // POS SIM, CAMERA SIM, CCTV, ROUTER SIM, GPS SIM
            $table->string('provider')->index(); // mtn, airtel, glo, 9mobile
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('available')->index(); // available, assigned, active
            $table->timestamps();
        });

        Schema::create('sim_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sim_id')->nullable()->constrained('sims')->nullOnDelete();
            $table->string('number');
            $table->string('category');
            $table->string('provider');
             $table->decimal('amount', 15, 2)->default(0.00);
            $table->string('request_type')->default('purchase'); // purchase, activation
            $table->string('status')->default('pending')->index(); // pending, approved, rejected
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sim_requests');
        Schema::dropIfExists('sims');
    }
};
