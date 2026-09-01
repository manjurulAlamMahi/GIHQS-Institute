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
        if (!Schema::hasTable('about_institutes')) {
            Schema::create('about_institutes', function (Blueprint $table) {
                $table->id();
                $table->string('title1');
                $table->string('title2')->nullable();
                $table->string('tag_line')->nullable();
                $table->longText('description')->nullable();
                $table->string('image')->nullable();
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
        Schema::dropIfExists('about_institutes');
    }
};
