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
        Schema::create('user_video_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('video_id')->nullable()->constrained('catalogue_videos')->onDelete('cascade');
            $table->foreignId('video_link_id')->nullable()->constrained('catalogue_video_links')->onDelete('cascade');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'video_id']);
            $table->index(['user_id', 'video_link_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_video_progress');
    }
};
