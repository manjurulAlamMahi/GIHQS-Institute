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
        Schema::create('accreditation_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reference_number')->nullable()->index();
            $table->string('verification_code')->nullable()->unique();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('certificate_pdf')->nullable();

            // 1. Applicant Information
            $table->string('applicant_category');
            $table->string('applicant_name');
            $table->string('department_division')->nullable();
            $table->string('country');
            $table->string('city');
            $table->string('website_url')->nullable();
            $table->string('year_established')->nullable();

            // 2. Program Information
            $table->string('program_name');
            $table->string('program_type');
            $table->string('program_delivery_format');
            $table->string('estimated_annual_participants')->nullable();
            $table->string('primary_language_of_instruction')->nullable();
            $table->string('program_launch_date')->nullable(); // Month / Year (e.g. MM/YYYY)

            // 3. Primary Contact Information
            $table->string('primary_contact_person');
            $table->string('contact_title_position');
            $table->string('email_address');
            $table->string('phone_number')->nullable();

            // 4. Supporting Attachments
            $table->string('program_overview_doc')->nullable();
            $table->string('governance_policy_doc')->nullable();

            // 5. Additional Information
            $table->text('additional_information')->nullable();

            // Admin management
            $table->string('status')->default('pending')->comment('pending, under_review, valid, revoked, expired, canceled');
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
        Schema::dropIfExists('accreditation_applications');
    }
};
