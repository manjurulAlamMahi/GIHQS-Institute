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
        // 1. Advisory Panel Header
        if (!Schema::hasTable('advisory_headers')) {
            Schema::create('advisory_headers', function (Blueprint $table) {
                $table->id();
                $table->string('title1');
                $table->string('title2')->nullable();
                $table->string('tagline')->nullable();
                $table->text('description')->nullable();
                $table->string('content_file')->nullable();
                $table->boolean('injected_status')->default(1);
                $table->timestamps();
            });
        }

        // 2. Advisory Focus
        if (!Schema::hasTable('advisory_focuses')) {
            Schema::create('advisory_focuses', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Advisory Focus Features
        if (!Schema::hasTable('advisory_focus_features')) {
            Schema::create('advisory_focus_features', function (Blueprint $table) {
                $table->id();
                $table->foreignId('advisory_focus_id')
                      ->constrained('advisory_focuses')
                      ->onDelete('cascade');
                $table->text('description');
                $table->timestamps();
            });
        }

        // 4. Advisory Scopes
        if (!Schema::hasTable('advisory_scopes')) {
            Schema::create('advisory_scopes', function (Blueprint $table) {
                $table->id();
                $table->string('title1');
                $table->string('title2')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 5. Advisory Scopes Features
        if (!Schema::hasTable('advisory_scope_features')) {
            Schema::create('advisory_scope_features', function (Blueprint $table) {
                $table->id();
                $table->foreignId('advisory_scope_id')
                      ->constrained('advisory_scopes')
                      ->onDelete('cascade');
                $table->string('icon')->nullable();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 6. Advisory Deliverable Card
        if (!Schema::hasTable('advisory_deliverable_cards')) {
            Schema::create('advisory_deliverable_cards', function (Blueprint $table) {
                $table->id();
                $table->string('title1');
                $table->string('title2')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 7. Advisory Deliverable Card Features
        if (!Schema::hasTable('advisory_deliverable_card_features')) {
            Schema::create('advisory_deliverable_card_features', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('advisory_deliverable_card_id');
                $table->foreign('advisory_deliverable_card_id', 'adv_deliv_card_id_foreign')
                      ->references('id')
                      ->on('advisory_deliverable_cards')
                      ->onDelete('cascade');
                $table->string('name');
                $table->timestamps();
            });
        }

        // 8. Advisory Services
        if (!Schema::hasTable('advisory_services')) {
            Schema::create('advisory_services', function (Blueprint $table) {
                $table->id();
                $table->string('title1');
                $table->string('title2')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 9. Advisory Service Features
        if (!Schema::hasTable('advisory_service_features')) {
            Schema::create('advisory_service_features', function (Blueprint $table) {
                $table->id();
                $table->foreignId('advisory_service_id')
                      ->constrained('advisory_services')
                      ->onDelete('cascade');
                $table->string('serial_number')->nullable();
                $table->string('tagline')->nullable();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 10. Advisory Discuss Card
        if (!Schema::hasTable('advisory_discuss_cards')) {
            Schema::create('advisory_discuss_cards', function (Blueprint $table) {
                $table->id();
                $table->string('title1');
                $table->string('title2')->nullable();
                $table->text('description')->nullable();
                $table->string('button_text')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advisory_discuss_cards');
        Schema::dropIfExists('advisory_service_features');
        Schema::dropIfExists('advisory_services');
        Schema::dropIfExists('advisory_deliverable_card_features');
        Schema::dropIfExists('advisory_deliverable_cards');
        Schema::dropIfExists('advisory_scope_features');
        Schema::dropIfExists('advisory_scopes');
        Schema::dropIfExists('advisory_focus_features');
        Schema::dropIfExists('advisory_focuses');
        Schema::dropIfExists('advisory_headers');
    }
};
