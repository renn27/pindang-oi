<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penugasans', function (Blueprint $table) {
            $table->index(['id_sub_kegiatan', 'tanggal_mulai'], 'penugasans_sub_tanggal_idx');
            $table->index(['id_anggota', 'butuh_dl', 'butuh_translok', 'tanggal_mulai', 'tanggal_selesai'], 'penugasans_anggota_travel_tanggal_idx');
            $table->index(['tanggal_mulai', 'tanggal_selesai'], 'penugasans_tanggal_range_idx');
        });

        Schema::table('pengirimans', function (Blueprint $table) {
            $table->index(['id_penugasan', 'created_at'], 'pengirimans_penugasan_created_idx');
            $table->index(['id_penugasan', 'bulan_pengiriman', 'created_at'], 'pengirimans_penugasan_bulan_created_idx');
        });

        Schema::table('penerimaans', function (Blueprint $table) {
            $table->index(['id_pengiriman', 'status', 'created_at'], 'penerimaans_pengiriman_status_created_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id', 'read_at', 'created_at'], 'notifications_notifiable_read_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_notifiable_read_created_idx');
        });

        Schema::table('penerimaans', function (Blueprint $table) {
            $table->dropIndex('penerimaans_pengiriman_status_created_idx');
        });

        Schema::table('pengirimans', function (Blueprint $table) {
            $table->dropIndex('pengirimans_penugasan_created_idx');
            $table->dropIndex('pengirimans_penugasan_bulan_created_idx');
        });

        Schema::table('penugasans', function (Blueprint $table) {
            $table->dropIndex('penugasans_sub_tanggal_idx');
            $table->dropIndex('penugasans_anggota_travel_tanggal_idx');
            $table->dropIndex('penugasans_tanggal_range_idx');
        });
    }
};
