<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_fields_id')->nullable()->constrained('service_fields')->onDelete('cascade');

            // Target a specific user OR a role group (not both required)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('user_type', ['personal', 'agent', 'partner', 'business', 'staff', 'checker', 'super_admin'])->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('commission', 10, 2)->default(0.00);
            $table->timestamps();

            // Prevent duplicate price entries per user + service + field combination
            $table->unique(['service_id', 'service_fields_id', 'user_id', 'user_type'], 'service_prices_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_prices');
    }
};
