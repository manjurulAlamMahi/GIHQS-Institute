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
        // Create the unified purchases table
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('purchase_type'); // 'catalogue' or 'membership'
            $table->foreignId('catalogue_id')->nullable()->constrained('catalogues')->onDelete('cascade');
            $table->foreignId('membership_package_id')->nullable()->constrained('membership_packages')->onDelete('cascade');
            $table->string('order_id')->unique();
            $table->string('stripe_session_id')->unique()->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('price_regular', 10, 2)->nullable();
            $table->decimal('price_purchased', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->string('price_type')->default('regular');
            $table->string('payment_status')->default('pending');
            $table->string('order_status')->default('pending')->comment('pending, accepted, active, cancelled, completed');
            $table->string('payment_method')->nullable();
            $table->timestamp('expires_at')->nullable()->comment('Expiration date of subscription access');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
