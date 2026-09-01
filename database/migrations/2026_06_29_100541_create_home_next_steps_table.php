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
        Schema::create('home_next_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_gihq_id')
                  ->constrained('home_gihqs')
                  ->onDelete('cascade');
            $table->string('title1');
            $table->string('title2')->nullable();
            $table->string('tagline')->nullable();
            $table->string('certificate_btn_text')->nullable();
            $table->string('learning_btn_text')->nullable();
            $table->string('advisory_btn_text')->nullable();
            $table->string('member_btn_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_next_steps');
    }
};
