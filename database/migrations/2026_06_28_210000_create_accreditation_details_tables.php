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
        Schema::dropIfExists('accreditation_insights_features');
        Schema::dropIfExists('accreditation_insights');
        Schema::dropIfExists('accreditation_domain_features');
        Schema::dropIfExists('accreditation_domains');
        Schema::dropIfExists('accreditation_process_features');
        Schema::dropIfExists('accreditation_processes');
        Schema::dropIfExists('accreditation_eligibility_features');
        Schema::dropIfExists('accreditation_eligibility');

        // 1. Eligibility Section
        Schema::create('accreditation_eligibility', function (Blueprint $table) {
            $table->id();
            $table->string('title1');
            $table->string('title2')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('accreditation_eligibility_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accreditation_eligibility_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('accreditation_eligibility_id', 'fk_accred_elig')
                ->references('id')->on('accreditation_eligibility')
                ->onDelete('cascade');
        });

        // 2. Process Section
        Schema::create('accreditation_processes', function (Blueprint $table) {
            $table->id();
            $table->string('title1');
            $table->string('title2')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('accreditation_process_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accreditation_process_id');
            $table->string('serial')->nullable();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('accreditation_process_id', 'fk_accred_proc')
                ->references('id')->on('accreditation_processes')
                ->onDelete('cascade');
        });

        // 3. Domain Section
        Schema::create('accreditation_domains', function (Blueprint $table) {
            $table->id();
            $table->string('title1');
            $table->string('title2')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('accreditation_domain_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accreditation_domain_id');
            $table->string('domain_serial')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('accreditation_domain_id', 'fk_accred_dom')
                ->references('id')->on('accreditation_domains')
                ->onDelete('cascade');
        });

        // 4. Insights Section
        Schema::create('accreditation_insights', function (Blueprint $table) {
            $table->id();
            $table->string('title1');
            $table->string('title2')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('accreditation_insights_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accreditation_insights_id');
            $table->string('title');
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('accreditation_insights_id', 'fk_accred_ins')
                ->references('id')->on('accreditation_insights')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accreditation_insights_features');
        Schema::dropIfExists('accreditation_insights');
        Schema::dropIfExists('accreditation_domain_features');
        Schema::dropIfExists('accreditation_domains');
        Schema::dropIfExists('accreditation_process_features');
        Schema::dropIfExists('accreditation_processes');
        Schema::dropIfExists('accreditation_eligibility_features');
        Schema::dropIfExists('accreditation_eligibility');
    }
};
