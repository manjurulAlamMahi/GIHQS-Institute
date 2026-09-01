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
        Schema::table('advisory_requests', function (Blueprint $table) {
            $table->decimal('payment_amount', 10, 2)->nullable()->after('price');
            $table->string('payment_currency')->nullable()->default('usd')->after('payment_amount');
            $table->text('payment_description')->nullable()->after('payment_currency');
            $table->text('stripe_payment_link')->nullable()->after('stripe_session_id');
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_payment_link');
            $table->timestamp('payment_sent_at')->nullable()->after('stripe_payment_intent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advisory_requests', function (Blueprint $table) {
            $table->dropColumn([
                'payment_amount',
                'payment_currency',
                'payment_description',
                'stripe_payment_link',
                'stripe_payment_intent_id',
                'payment_sent_at',
            ]);
        });
    }
};
