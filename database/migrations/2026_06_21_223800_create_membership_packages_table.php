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
        if (!Schema::hasTable('membership_packages')) {
            Schema::create('membership_packages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('title');
                $table->text('short_description')->nullable();
                $table->decimal('price', 8, 2)->default(0.00);
                $table->decimal('discount_percentage', 5, 2)->default(0.00);
                $table->unsignedInteger('validity_days')->nullable()->comment('Duration of membership package in days');
                $table->integer('exam_attempt_limit')->default(1);
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
        Schema::dropIfExists('membership_packages');
    }
};
