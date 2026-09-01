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
        Schema::create('pathway_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('pathway_questions')->onDelete('cascade');
            $table->string('option_text');
            $table->unsignedBigInteger('next_question_id')->nullable();
            $table->unsignedBigInteger('result_id')->nullable();
            $table->integer('order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('next_question_id')->references('id')->on('pathway_questions')->onDelete('set null');
            $table->foreign('result_id')->references('id')->on('pathway_results')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pathway_options');
    }
};
