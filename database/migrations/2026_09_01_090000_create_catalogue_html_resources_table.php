<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogue_html_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogue_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            // A label for grouping and iconography only - it does not affect rendering.
            $table->string('kind')->default('module');
            $table->string('file_path');
            // Public documents bypass the catalogue entitlement check.
            $table->boolean('is_public')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['catalogue_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogue_html_resources');
    }
};
