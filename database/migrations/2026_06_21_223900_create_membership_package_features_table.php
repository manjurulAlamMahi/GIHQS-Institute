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
        if (!Schema::hasTable('membership_package_features')) {
            Schema::create('membership_package_features', function (Blueprint $table) {
                $table->id();
                $table->foreignId('membership_package_id')
                      ->constrained('membership_packages')
                      ->onDelete('cascade');
                $table->text('description');
                $table->string('badge')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_package_features');
    }
};
