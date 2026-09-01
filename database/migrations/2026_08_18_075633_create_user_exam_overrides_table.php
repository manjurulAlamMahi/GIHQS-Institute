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
        Schema::create('user_exam_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('catalogue_exam_id')->constrained('catalogue_exams')->onDelete('cascade');
            $table->integer('max_attempts')->nullable();
            $table->date('retake_eligible_date')->nullable();
            $table->boolean('ignore_cooldown')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'catalogue_exam_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_exam_overrides');
    }
};
