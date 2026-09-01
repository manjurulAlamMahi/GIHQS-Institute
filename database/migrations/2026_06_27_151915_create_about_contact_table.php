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
        if (!Schema::hasTable('about_contact')) {
            Schema::create('about_contact', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->longText('working_hours')->nullable();
                $table->text('mission')->nullable();
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
        Schema::dropIfExists('about_contact');
    }
};
