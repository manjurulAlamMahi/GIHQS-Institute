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
        // 1. Home GIHQ & Professional Ecosystem Table
        Schema::create('home_gihqs', function (Blueprint $table) {
            $table->id();
            // Home GIHQ section
            $table->string('title1');
            $table->string('title2')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('certificate_btn_text')->nullable();
            $table->string('learning_btn_text')->nullable();
            $table->string('advisory_btn_text')->nullable();
            $table->string('member_btn_text')->nullable();

            // Professional Ecosystem section
            $table->string('professional_ecosystem_title')->nullable();
            $table->string('learning_tagline')->nullable();
            $table->string('learning_title')->nullable();
            $table->text('learning_details')->nullable();
            $table->string('certificate_tagline')->nullable();
            $table->string('certificate_title')->nullable();
            $table->text('certificate_details')->nullable();
            $table->string('lead_tagline')->nullable();
            $table->string('lead_title')->nullable();
            $table->text('lead_details')->nullable();

            // Common inject features
            $table->string('content_file')->nullable();
            $table->boolean('injected_status')->default(1);

            $table->timestamps();
        });

        // 2. Services & Pathways Table (repeater)
        Schema::create('home_services_pathways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_gihq_id')
                  ->constrained('home_gihqs')
                  ->onDelete('cascade');
            $table->string('serial')->nullable();
            $table->string('target_audience')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('link_text')->nullable();
            $table->timestamps();
        });

        // 3. The GIHQS Professional Pathways Table (repeater)
        Schema::create('home_professional_pathways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_gihq_id')
                  ->constrained('home_gihqs')
                  ->onDelete('cascade');
            $table->string('serial')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('link_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_professional_pathways');
        Schema::dropIfExists('home_services_pathways');
        Schema::dropIfExists('home_gihqs');
    }
};
