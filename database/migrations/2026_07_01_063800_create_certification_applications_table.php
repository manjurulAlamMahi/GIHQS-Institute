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
        Schema::create('certification_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference_number')->nullable()->index();

            // 1. Applicant Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('country');
            $table->string('city');
            $table->string('current_job_title');
            $table->string('organization');
            $table->string('linkedin_profile')->nullable();

            // 2. Professional Background
            $table->string('years_of_experience');       // e.g. "0-2", "3-5", "5-10", "10+"
            $table->string('primary_area_of_experience');
            $table->string('professional_role');
            $table->string('resume_cv')->nullable();     // stored file path

            // 3. Certification Selection
            $table->foreignId('catalogue_id')->nullable()->constrained('catalogues')->nullOnDelete();
            
            // 4. Review & Submit confirmations
            $table->boolean('confirm_accuracy')->default(false);
            $table->boolean('agree_policies')->default(false);

            // Admin management
            $table->string('status')->default('pending')->comment('pending, reviewing, approved, rejected');
            $table->text('admin_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certification_applications');
    }
};
