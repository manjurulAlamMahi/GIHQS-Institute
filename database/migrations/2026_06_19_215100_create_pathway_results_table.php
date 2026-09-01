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
        Schema::create('pathway_results', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('badges')->nullable();
            $table->text('info_box_text')->nullable();
            $table->string('primary_button_text')->default('Apply for Accreditation');
            $table->string('primary_button_url')->nullable();
            $table->string('secondary_button_text')->default('View Accreditation');
            $table->string('secondary_button_url')->nullable();
            $table->tinyInteger('status')->default(1); // 1 = Active, 0 = Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pathway_results');
    }
};
