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
        Schema::create('sme_data', function (Blueprint $table) {
            $table->id();
            $table->string('data_id')->unique();
            $table->string('network');
            $table->string('plan_type');
            $table->decimal('personal_price', 10, 2);
            $table->decimal('agent_price', 10, 2);
            $table->decimal('partner_price', 10, 2);
            $table->decimal('business_price', 10, 2);
            $table->string('size');
            $table->string('validity');
            $table->enum('status', ['enabled', 'disabled'])->default('enabled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sme_data');
    }
};
