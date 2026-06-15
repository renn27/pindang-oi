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
        Schema::table('agenda_pimpinans', function (Blueprint $table) {
            $table->boolean('butuh_dl')->default(false)->after('status');
        });

        Schema::table('kalenderdls', function (Blueprint $table) {
            $table->uuid('id_agenda_pimpinan')->nullable()->after('id_penugasan');
            $table->foreign('id_agenda_pimpinan')
                ->references('id_agenda')
                ->on('agenda_pimpinans')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kalenderdls', function (Blueprint $table) {
            $table->dropForeign(['id_agenda_pimpinan']);
            $table->dropColumn('id_agenda_pimpinan');
        });

        Schema::table('agenda_pimpinans', function (Blueprint $table) {
            $table->dropColumn('butuh_dl');
        });
    }
};
