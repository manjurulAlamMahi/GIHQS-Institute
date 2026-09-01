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
            $table->string('stripe_customer_id')->nullable()->after('email');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            $table->string('stripe_subscription_status')->nullable()->after('stripe_subscription_id');
            $table->timestamp('stripe_subscription_period_start')->nullable()->after('stripe_subscription_status');
            $table->timestamp('stripe_subscription_period_end')->nullable()->after('stripe_subscription_period_start');
            $table->timestamp('stripe_next_renewal_at')->nullable()->after('stripe_subscription_period_end');
            $table->boolean('stripe_subscription_cancel_at_period_end')->default(false)->after('stripe_next_renewal_at');
            $table->foreignId('membership_package_id')->nullable()->after('stripe_subscription_cancel_at_period_end')->constrained('membership_packages')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('membership_package_id');
            $table->dropColumn([
                'stripe_customer_id',
                'stripe_subscription_id',
                'stripe_subscription_status',
                'stripe_subscription_period_start',
                'stripe_subscription_period_end',
                'stripe_next_renewal_at',
                'stripe_subscription_cancel_at_period_end',
            ]);
        });
    }
};
