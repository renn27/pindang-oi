<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->date('inactive_from_month')->nullable()->after('is_active');
        });

        DB::table('pegawais')
            ->where('is_active', false)
            ->whereNull('inactive_from_month')
            ->update(['inactive_from_month' => now()->startOfMonth()->toDateString()]);
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn('inactive_from_month');
        });
    }
};
