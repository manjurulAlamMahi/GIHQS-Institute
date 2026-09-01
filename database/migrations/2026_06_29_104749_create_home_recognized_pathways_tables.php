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
        // 1. Recognized Pathways main page details table
        Schema::create('home_recognized_pathways', function (Blueprint $table) {
            $table->id();
            $table->string('title1');
            $table->string('title2')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();

            // Content Injection fields
            $table->string('content_file')->nullable();
            $table->boolean('injected_status')->default(1);

            $table->timestamps();
        });

        // 2. Certificates repeater table
        Schema::create('home_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_recognized_pathway_id')
                  ->constrained('home_recognized_pathways')
                  ->onDelete('cascade');
            $table->string('short_title')->nullable();
            $table->string('title');
            $table->string('icon')->nullable();
            $table->string('tagline')->nullable();
            $table->string('headline')->nullable();
            $table->text('description')->nullable();
            $table->string('audience')->nullable();
            $table->text('tags')->nullable(); // Comma-separated tags
            $table->string('button_text')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_certificates');
        Schema::dropIfExists('home_recognized_pathways');
    }
};
