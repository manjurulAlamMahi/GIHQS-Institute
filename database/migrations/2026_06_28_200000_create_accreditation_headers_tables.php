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
        Schema::create('accreditation_headers', function (Blueprint $table) {
            $table->id();
            $table->string('title1');
            $table->string('title2')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->text('note')->nullable();
            $table->string('apply_btn_text')->nullable();
            $table->string('download_btn_text')->nullable();
            $table->string('download_file')->nullable();
            $table->string('content_file')->nullable();
            $table->boolean('injected_status')->default(1);
            $table->timestamps();
        });

        Schema::create('accreditation_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accreditation_header_id')
                ->constrained('accreditation_headers')
                ->onDelete('cascade');
            $table->string('tagname');
            $table->timestamps();
        });

        Schema::create('accreditation_keyfacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accreditation_header_id')
                ->constrained('accreditation_headers')
                ->onDelete('cascade');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accreditation_keyfacts');
        Schema::dropIfExists('accreditation_tags');
        Schema::dropIfExists('accreditation_headers');
    }
};
