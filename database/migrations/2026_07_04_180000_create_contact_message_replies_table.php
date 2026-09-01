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
        Schema::create('contact_message_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_message_id')->constrained('contact_messages')->cascadeOnDelete();
            $table->string('subject');
            $table->text('message');
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_message_replies');
    }
};
