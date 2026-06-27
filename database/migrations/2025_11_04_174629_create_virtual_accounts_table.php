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
        Schema::create('virtual_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('account_reference')->unique()->comment('Unique reference from payment provider');
            $table->string('account_number', 20);
            $table->string('account_name');
            $table->string('bank_name');
            $table->string('bank_code', 10)->nullable();
            $table->string('provider')->default('monnify')->comment('Payment provider: monnify, paystack, flutterwave');

            $table->boolean('is_active')->default(false)->comment('True once provider confirms account creation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_accounts');
    }
};
