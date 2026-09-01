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
        if (!Schema::hasTable('vision_mission_values')) {
            Schema::create('vision_mission_values', function (Blueprint $table) {
                $table->id();

                // General Section
                $table->string('tagline')->nullable();
                $table->string('title1')->nullable();
                $table->string('title2')->nullable();
                $table->text('short_description')->nullable();

                // Vision Section
                $table->string('vision_tagline')->nullable();
                $table->string('vision_title')->nullable();
                $table->text('vision_short_description')->nullable();

                // Mission Section
                $table->string('mission_tagline')->nullable();
                $table->string('mission_title')->nullable();
                $table->text('mission_short_description')->nullable();

                // Value Section
                $table->string('value_tagline')->nullable();
                $table->string('value_title')->nullable();
                $table->string('value_title2')->nullable();
                $table->text('value_short_description')->nullable();

                // Global Perspective Section
                $table->string('global_perspective_tagline')->nullable();
                $table->string('global_perspective_title')->nullable();
                $table->text('global_perspective_short_description')->nullable();

                // Integrity Section
                $table->string('integrity_tagline')->nullable();
                $table->string('integrity_title')->nullable();
                $table->text('integrity_short_description')->nullable();

                // Human Centered Section
                $table->string('human_centered_tagline')->nullable();
                $table->string('human_centered_title')->nullable();
                $table->text('human_centered_short_description')->nullable();

                // Quality & Excellence Section
                $table->string('quality_excellence_tagline')->nullable();
                $table->string('quality_excellence_title')->nullable();
                $table->text('quality_excellence_short_description')->nullable();

                // Safety Leadership Section
                $table->string('safety_leadership_tagline')->nullable();
                $table->string('safety_leadership_title')->nullable();
                $table->text('safety_leadership_short_description')->nullable();

                $table->string('content_file')->nullable();
                $table->boolean('injected_status')->default(1);

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vision_mission_values');
    }
};
