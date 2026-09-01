<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('accreditation_fees_plan_features');
        Schema::dropIfExists('accreditation_fees_plans');
        Schema::dropIfExists('accreditation_fees');

        // Main config table
        Schema::create('accreditation_fees', function (Blueprint $table) {
            $table->id();
            $table->string('title1');
            $table->string('title2')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Plans repeater table
        Schema::create('accreditation_fees_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accreditation_fee_id');
            $table->string('title');
            $table->string('price')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('accreditation_fee_id', 'fk_fees_plan_fee')
                ->references('id')->on('accreditation_fees')
                ->onDelete('cascade');
        });

        // Plan features nested repeater table
        Schema::create('accreditation_fees_plan_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accreditation_fees_plan_id');
            $table->text('feature');
            $table->timestamps();

            $table->foreign('accreditation_fees_plan_id', 'fk_fees_feat_plan')
                ->references('id')->on('accreditation_fees_plans')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accreditation_fees_plan_features');
        Schema::dropIfExists('accreditation_fees_plans');
        Schema::dropIfExists('accreditation_fees');
    }
};
