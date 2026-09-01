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
        Schema::create('ce_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('catalogue_id')->nullable()->constrained('catalogues')->onDelete('cascade');
            $table->string('domain');
            $table->string('activity_type');
            $table->string('activity_title');
            $table->string('provider');
            $table->date('completion_date');
            $table->decimal('credits_earned', 8, 2)->default(0.00);
            $table->string('evidence_file')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ce_activities');
    }
};
