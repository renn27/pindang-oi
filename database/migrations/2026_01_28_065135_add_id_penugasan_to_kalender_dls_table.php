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
        Schema::table('kalenderdls', function (Blueprint $table) {
            $table->foreignUuid('id_penugasan')
                ->nullable()
                ->after('id_pegawai')
                ->constrained('penugasans', 'id_penugasan')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kalenderdls', function (Blueprint $table) {
            $table->dropForeign(['id_penugasan']);
            $table->dropColumn('id_penugasan');
        });
    }
};
