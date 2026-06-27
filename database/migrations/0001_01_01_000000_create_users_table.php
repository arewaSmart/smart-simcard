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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Personal Information
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('profile_photo')->nullable();

            // Role & Account Status
            $table->enum('role', ['personal', 'agent', 'partner', 'business', 'staff', 'checker', 'super_admin'])->default('personal');
            $table->enum('status', ['active', 'suspended', 'inactive', 'banned'])->default('active');
            $table->tinyInteger('account_tier')->default(0)->comment('KYC tier: 0=unverified, 1=basic, 2=intermediate, 3=full');

            // Contact & Credentials
            $table->string('email')->unique();
            $table->string('phone')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->string('transaction_pin')->nullable()->comment('Hashed 4/6-digit PIN for authorising transactions');
            $table->timestamp('pin_set_at')->nullable();

            // KYC & Verification
            $table->string('bvn')->nullable()->unique();
            $table->string('nin')->nullable()->unique()->comment('National Identification Number');
            $table->date('date_of_birth')->nullable();

            // OTP Verification
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();

            // Location Details
            $table->string('state')->nullable();
            $table->string('lga')->nullable();
            $table->string('address')->nullable();

            // Business / Agent Details (used when role = business | agent | partner)
            $table->string('business_name')->nullable();
            $table->string('business_type')->nullable()->comment('e.g. sole_proprietor, llc, partnership');
            $table->string('cac_number')->nullable()->comment('Corporate Affairs Commission registration number');

            // Referral System
            $table->string('referral_code', 20)->nullable()->unique();
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete();

            // Limits & Metadata
            $table->decimal('limit', 15, 2)->default(20000.00)->comment('Single-transaction spending limit');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->text('suspension_reason')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
