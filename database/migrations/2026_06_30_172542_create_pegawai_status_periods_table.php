<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai_status_periods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_pegawai');
            $table->string('status'); // 'Aktif', 'Nonaktif'
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->foreign('id_pegawai')
                ->references('id_pegawai')
                ->on('pegawais')
                ->onDelete('cascade');

            $table->index(['id_pegawai', 'status', 'start_date', 'end_date'], 'peg_status_idx');
        });

        // Migrasi data lama dari tabel pegawais ke pegawai_status_periods
        $pegawais = DB::table('pegawais')->whereNull('deleted_at')->get();
        foreach ($pegawais as $pegawai) {
            if (is_null($pegawai->inactive_from_month)) {
                // Pegawai aktif terus menerus
                DB::table('pegawai_status_periods')->insert([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'id_pegawai' => $pegawai->id_pegawai,
                    'status' => 'Aktif',
                    'start_date' => $pegawai->created_at ? Carbon::parse($pegawai->created_at)->startOfMonth()->toDateString() : '2020-01-01',
                    'end_date' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Pegawai nonaktif dari bulan tertentu
                $inactiveStart = Carbon::parse($pegawai->inactive_from_month)->startOfMonth();
                $activeEnd = $inactiveStart->copy()->subDay()->toDateString();
                $createdDate = $pegawai->created_at ? Carbon::parse($pegawai->created_at)->startOfMonth() : Carbon::parse('2020-01-01');

                // Jika tanggal buat ternyata lebih besar atau sama dengan bulan nonaktif,
                // set awal aktif ke 1 bulan sebelumnya agar rentangnya valid
                if ($createdDate->greaterThanOrEqualTo($inactiveStart)) {
                    $createdDate = $inactiveStart->copy()->subMonth();
                }

                DB::table('pegawai_status_periods')->insert([
                    [
                        'id' => (string) \Illuminate\Support\Str::uuid(),
                        'id_pegawai' => $pegawai->id_pegawai,
                        'status' => 'Aktif',
                        'start_date' => $createdDate->toDateString(),
                        'end_date' => $activeEnd,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'id' => (string) \Illuminate\Support\Str::uuid(),
                        'id_pegawai' => $pegawai->id_pegawai,
                        'status' => 'Nonaktif',
                        'start_date' => $inactiveStart->toDateString(),
                        'end_date' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_status_periods');
    }
};
