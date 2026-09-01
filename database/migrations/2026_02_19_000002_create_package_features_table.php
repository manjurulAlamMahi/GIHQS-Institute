<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('package_features')) {
            Schema::create('package_features', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
                $table->text('description');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('package_features');
    }
};
