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
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('country')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('password_reset_token')->nullable(); //optional
            $table->timestamp('password_reset_token_expired_at')->nullable(); //optional
            $table->string('avatar')->nullable();
            $table->string('role')->default('user')->comment('Possible values: admin, user, standard_member, premium_member, editor, manager, moderator');
            $table->decimal('user_latitude', 10, 7)->nullable(); //optional
            $table->decimal('user_longitude', 10, 7)->nullable(); //optional
            // address fields start ---
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->text('bio')->nullable();
            // otp fields start ---
            $table->string('otp', 6)->nullable();
            $table->boolean('otp_verified')->default(false);
            $table->unsignedInteger('otp_attempts')->default(0); //optional
            $table->timestamp('otp_expired_at')->nullable();
            $table->timestamp('otp_verified_at')->nullable(); //optional
            // social fields and fcm start ---
            $table->string('google_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('provider')->nullable(); //optional
            $table->text('provider_token')->nullable(); //optional
            $table->string('device_id')->nullable();
            $table->string('fcm_token')->nullable();
            // social fields and fcm end ---
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->string('language')->default('en');
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->timestamps();
            $table->softDeletes();
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
