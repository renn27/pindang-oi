<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    /**
     * Data bidang dari gambar Anda
     */
    private $dataBidang = [
        [
            'nama_bidang' => 'Kepala',
            'detail_bidang' => 'Kepala'
        ],
        [
            'nama_bidang' => 'SUB Bagian Umum',
            'detail_bidang' => 'Sub Bagian Umum'
        ],
        [
            'nama_bidang' => 'Fungsi Statistik Sosial',
            'detail_bidang' => 'Fungsi Statistik Sosial'
        ],
        [
            'nama_bidang' => 'Fungsi Statistik Produksi',
            'detail_bidang' => 'Fungsi Statistik Produksi'
        ],
        [
            'nama_bidang' => 'Fungsi Statistik Distribusi',
            'detail_bidang' => 'Fungsi Statistik Distribusi'
        ],
        [
            'nama_bidang' => 'Fungsi NWAS',
            'detail_bidang' => 'Fungsi NWAS'
        ],
        [
            'nama_bidang' => 'Fungsi SPBE',
            'detail_bidang' => 'Fungsi SPBE'
        ],
        [
            'nama_bidang' => 'Fungsi DLS',
            'detail_bidang' => 'Fungsi DLS'
        ],
        [
            'nama_bidang' => 'Fungsi SBUKB',
            'detail_bidang' => 'Statistik Berdampak untuk Kampus Berdampak'
        ],
        [
            'nama_bidang' => 'Fungsi Program Desa Cantik',
            'detail_bidang' => 'Fungsi Program Desa Cantik'
        ],
        [
            'nama_bidang' => 'Fungsi Pembinaan Statistik Sektoral',
            'detail_bidang' => 'Fungsi Pembinaan Statistik Sektoral'
        ],
        [
            'nama_bidang' => 'Fungsi Kehumasan',
            'detail_bidang' => 'Fungsi Kehumasan'
        ]
    ];
    
    public function run()
    {
        $this->command->info('🚀 Memulai seeding data bidang...');
        
        foreach ($this->dataBidang as $data) {
            // Cek apakah sudah ada
            $existing = Bidang::withTrashed()
                ->where('nama_bidang', $data['nama_bidang'])
                ->first();
            
            if ($existing) {
                // Update jika sudah ada
                $existing->update($data);
                $existing->restore(); // Pastikan tidak ter-soft delete
                $this->command->info("✅ Update: {$data['nama_bidang']}");
            } else {
                // Buat baru
                Bidang::create($data);
                $this->command->info("➕ Tambah: {$data['nama_bidang']}");
            }
        }
        
        $total = Bidang::count();
        $active = Bidang::active()->count();
        $trashed = Bidang::trashed()->count();
        
        $this->command->info("📊 Total: {$total} bidang");
        $this->command->info("🟢 Aktif: {$active} bidang");
        $this->command->info("🗑️  Trashed: {$trashed} bidang");
        $this->command->info('🎉 Seeding selesai!');
    }
}