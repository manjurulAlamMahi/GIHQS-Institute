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
        Schema::create('advisory_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference_number')->nullable()->index();
            $table->string('organization_name');
            $table->string('full_name');
            $table->string('work_email');
            $table->string('phone_number');
            $table->string('country');
            $table->string('organization_type');
            $table->string('service_of_interest');
            $table->string('desired_timeline');
            $table->text('description_of_needs');

            // Payment & Billing fields
            $table->decimal('price', 10, 2)->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->integer('validity_days')->nullable()->default(30);
            $table->timestamp('expires_at')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('stripe_session_id')->nullable();
            $table->text('payment_link')->nullable();

            // Admin management fields
            $table->string('status')->default('pending')->comment('pending, reviewing, completed, rejected');
            $table->text('admin_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advisory_requests');
    }
};
