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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('code');
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->string('highlight_title')->nullable();
            $table->decimal('regular_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->text('image')->nullable();
            $table->integer('stock')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1=Published, 0=Unpublished');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
