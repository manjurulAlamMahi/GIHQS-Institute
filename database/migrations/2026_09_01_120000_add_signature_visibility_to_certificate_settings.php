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
            $table->boolean('show_chairman')->default(true)->after('chairman_signature');
            $table->boolean('show_executive_director')->default(true)->after('executive_director_signature');
        });

        // Existing certificates carried both signatures, so keep them on.
        DB::table('certificate_settings')->update([
            'show_chairman'           => true,
            'show_executive_director' => true,
        ]);
    }

    public function down(): void
    {
        Schema::table('certificate_settings', function (Blueprint $table) {
            $table->dropColumn(['show_chairman', 'show_executive_director']);
        });
    }
};
