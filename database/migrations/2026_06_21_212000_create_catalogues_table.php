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
        if (!Schema::hasTable('catalogues')) {
            Schema::create('catalogues', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('short_title')->nullable();
                $table->text('short_description')->nullable();
                $table->decimal('price_regular', 8, 2)->default(0.00);
                $table->string('discount_type')->default('percentage');
                $table->decimal('discount_value', 8, 2)->default(0.00);
                $table->boolean('is_discount_active')->default(false);
                $table->decimal('price_final', 8, 2)->default(0.00);
                $table->string('catalogue_type')->default('paid');
                $table->string('service_type')->default('Certification');
                $table->string('details_file')->nullable();
                $table->string('module_file')->nullable();
                $table->decimal('credit_earn', 8, 2)->default(0.00);
                $table->decimal('ce_credit_total_required', 8, 2)->default(0.00);
                $table->decimal('ce_credit', 8, 2)->default(0.00);
                $table->decimal('passing_percentage', 5, 2)->default(70.00);
                $table->integer('validity_years')->default(1);
                $table->string('certification_seal')->nullable();
                $table->text('credential_statement')->nullable();
                $table->boolean('is_feature')->default(false);
                $table->boolean('is_trending')->default(false);
                $table->boolean('is_popular')->default(false);
                $table->boolean('healthcare_quality_improvement')->default(false);
                $table->boolean('patient_safety_risk_management')->default(false);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogues');
    }
};
