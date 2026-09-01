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
        Schema::table('website_settings', function (Blueprint $table) {
            if (Schema::hasColumn('website_settings', 'tax_percentage')) {
                $table->dropColumn('tax_percentage');
            }
            if (!Schema::hasColumn('website_settings', 'support_email')) {
                $table->string('support_email')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('website_settings', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2)->default(0.00)->nullable();
            }
            if (Schema::hasColumn('website_settings', 'support_email')) {
                $table->dropColumn('support_email');
            }
        });
    }
};
