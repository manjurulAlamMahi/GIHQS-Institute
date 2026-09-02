<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_settings', function (Blueprint $table) {
            $table->string('chairman_title')->nullable()->after('chairman_name');
            $table->string('executive_director_title')->nullable()->after('executive_director_name');
        });

        // Backfill with the strings that were previously hardcoded in the
        // templates, so existing certificates look exactly as they did.
        DB::table('certificate_settings')->update([
            'chairman_title'           => 'Chairman of the Board',
            'executive_director_title' => 'Executive Director',
        ]);
    }

    public function down(): void
    {
        Schema::table('certificate_settings', function (Blueprint $table) {
            $table->dropColumn(['chairman_title', 'executive_director_title']);
        });
    }
};
