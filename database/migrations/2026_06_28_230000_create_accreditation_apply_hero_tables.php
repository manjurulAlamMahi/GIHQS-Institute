<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('accreditation_eligibility_snapshot_features');
        Schema::dropIfExists('accreditation_eligibility_snapshot');
        Schema::dropIfExists('accreditation_apply_hero');

        // Section 1: Apply Hero
        Schema::create('accreditation_apply_hero', function (Blueprint $table) {
            $table->id();
            $table->string('title1');
            $table->string('title2')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // Section 2: Eligibility Snapshot main record
        Schema::create('accreditation_eligibility_snapshot', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Section 2: Eligibility Snapshot features (repeater)
        Schema::create('accreditation_eligibility_snapshot_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accreditation_eligibility_snapshot_id');
            $table->string('keypoints')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();

            $table->foreign('accreditation_eligibility_snapshot_id', 'fk_snap_feat')
                ->references('id')->on('accreditation_eligibility_snapshot')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accreditation_eligibility_snapshot_features');
        Schema::dropIfExists('accreditation_eligibility_snapshot');
        Schema::dropIfExists('accreditation_apply_hero');
    }
};
