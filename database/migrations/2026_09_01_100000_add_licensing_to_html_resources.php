<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalogue_html_resources', function (Blueprint $table) {
            // null means no key is required, so existing resources are unaffected.
            $table->string('access_key')->nullable()->after('is_public');
            // null means a redeemed licence never expires.
            $table->integer('license_validity_days')->nullable()->after('access_key');
        });

        Schema::create('html_resource_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalogue_html_resource_id')
                ->constrained('catalogue_html_resources')
                ->cascadeOnDelete();
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'catalogue_html_resource_id'], 'html_resource_licenses_user_resource_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('html_resource_licenses');

        Schema::table('catalogue_html_resources', function (Blueprint $table) {
            $table->dropColumn(['access_key', 'license_validity_days']);
        });
    }
};
