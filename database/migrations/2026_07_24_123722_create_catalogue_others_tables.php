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
        // 1. Update catalogue_exams to add exam_id and make columns nullable
        Schema::table('catalogue_exams', function (Blueprint $table) {
            $table->foreignId('exam_id')->nullable()->after('catalogue_id')->constrained('exams')->onDelete('cascade');
            $table->string('exam_title')->nullable()->change();
            $table->string('exam_link')->nullable()->change();
        });

        // 2. Create catalogue_live_links table
        Schema::create('catalogue_live_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogue_id')->constrained('catalogues')->onDelete('cascade');
            $table->string('link_title')->nullable();
            $table->string('link_url');
            $table->timestamps();
        });

        // 3. Create catalogue_videos table
        Schema::create('catalogue_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogue_id')->constrained('catalogues')->onDelete('cascade');
            $table->string('video_title')->nullable();
            $table->string('video_file');
            $table->timestamps();
        });

        // 4. Create catalogue_video_links table
        Schema::create('catalogue_video_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogue_id')->constrained('catalogues')->onDelete('cascade');
            $table->string('video_link_title')->nullable();
            $table->string('video_link_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogue_video_links');
        Schema::dropIfExists('catalogue_videos');
        Schema::dropIfExists('catalogue_live_links');

        Schema::table('catalogue_exams', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
            $table->dropColumn('exam_id');
            $table->string('exam_title')->nullable(false)->change();
        });
    }
};
