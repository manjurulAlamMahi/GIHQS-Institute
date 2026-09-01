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
        Schema::create('policies_governances', function (Blueprint $table) {
            $table->id();
            $table->string('title1');
            $table->string('title2')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();

            $table->string('inst_title')->nullable();
            $table->string('inst_tag')->nullable();
            $table->text('inst_description')->nullable();

            $table->string('cert_title')->nullable();
            $table->string('cert_tag')->nullable();
            $table->text('cert_description')->nullable();

            $table->string('acc_title')->nullable();
            $table->string('acc_tag')->nullable();
            $table->text('acc_description')->nullable();

            $table->string('commitment_title1')->nullable();
            $table->string('commitment_title2')->nullable();
            $table->text('commitment_description')->nullable();
            $table->string('content_file')->nullable();
            $table->boolean('injected_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies_governances');
    }
};
