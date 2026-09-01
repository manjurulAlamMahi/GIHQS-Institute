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
        if (!Schema::hasTable('catalogue_features')) {
            Schema::create('catalogue_features', function (Blueprint $table) {
                $table->id();
                $table->foreignId('catalogue_id')
                      ->constrained('catalogues')
                      ->onDelete('cascade');
                $table->text('description');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogue_features');
    }
};
