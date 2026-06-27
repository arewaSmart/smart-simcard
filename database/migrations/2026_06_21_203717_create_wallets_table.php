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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Balances
            $table->decimal('balance', 15, 2)->default(0.00)->comment('Available balance');
            $table->decimal('bonus', 15, 2)->default(0.00)->comment('Bonus/promo balance');
            $table->decimal('hold_amount', 15, 2)->default(0.00)->comment('Amount on hold / pending');
            $table->decimal('total_credited', 15, 2)->default(0.00)->comment('Lifetime total funded');
            $table->decimal('total_debited', 15, 2)->default(0.00)->comment('Lifetime total spent');

            // Identity
            $table->string('wallet_number')->unique();
            $table->string('currency', 10)->default('NGN');

            // Limits
            $table->decimal('daily_limit', 15, 2)->default(100000.00)->comment('Max spend per day');
            $table->decimal('monthly_limit', 15, 2)->default(1000000.00)->comment('Max spend per month');

            // Status & Control
            $table->enum('status', ['active', 'suspended', 'frozen', 'closed'])->default('active');
            $table->boolean('is_locked')->default(false)->comment('Temporary freeze — blocks debits');

            // Timestamps
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
