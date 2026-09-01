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
        Schema::create('accreditation_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('title1');
            $table->string('title2')->nullable();
            $table->string('tagline')->nullable();
            $table->text('short_description')->nullable();

            $table->string('purpose_tagline')->nullable();
            $table->string('purpose_title')->nullable();
            $table->text('purpose_short_description')->nullable();

            $table->string('review_title')->nullable();

            $table->string('panel_title')->nullable();
            $table->text('panel_short_description')->nullable();

            $table->string('appointment_title')->nullable();
            $table->text('appointment_short_description')->nullable();

            $table->string('conflict_title')->nullable();
            $table->text('conflict_short_description')->nullable();

            $table->string('expression_title')->nullable();
            $table->text('expression_description')->nullable();
            $table->string('content_file')->nullable();
            $table->boolean('injected_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accreditation_reviews');
    }
};
