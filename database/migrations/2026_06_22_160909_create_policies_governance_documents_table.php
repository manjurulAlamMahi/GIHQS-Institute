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
        Schema::create('policies_governance_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policies_governance_id')
                  ->constrained('policies_governances')
                  ->onDelete('cascade');
            $table->string('type'); // 'institutional', 'certification', 'accreditation'
            $table->string('title');
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies_governance_documents');
    }
};
