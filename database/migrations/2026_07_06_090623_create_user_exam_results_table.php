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
        Schema::create('user_exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('catalogue_exam_id')->constrained('catalogue_exams')->onDelete('cascade');
            $table->decimal('score', 8, 2)->nullable();
            $table->decimal('points_available', 10, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('percentage_passmark', 10, 2)->nullable();
            $table->string('status')->nullable(); // passed, failed
            $table->string('duration')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('classmarker_result_id')->unique()->nullable();
            $table->string('certificate_serial_number')->nullable();
            $table->text('certificate_url')->nullable();
            $table->text('download_certificate')->nullable();
            $table->text('view_results_url')->nullable();
            $table->json('raw_payload')->nullable();
            $table->json('category_results')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_exam_results');
    }
};
