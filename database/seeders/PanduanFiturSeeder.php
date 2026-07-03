<?php

namespace Database\Seeders;

use App\Models\PanduanFitur;
use Illuminate\Database\Seeder;

class PanduanFiturSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clean existing documentation data
        PanduanFitur::truncate();

        $data = [
            // ==================== ADMIN ROLE ====================
            [
                'type' => 'user',
                'role_tab' => 'Admin',
                'menu_name' => 'Kelola Bidang',
                'slug' => 'admin-kelola-bidang',
                'title' => 'Manajemen Bidang Kerja',
                'explanation' => 'Mengatur struktur bidang kerja organisasi untuk mengelompokkan kegiatan dan penugasan secara teratur.',
                'route_target' => 'bidang.index',
                'roles_allowed' => ['Admin'],
                'output' => 'Daftar bidang kerja aktif yang terintegrasi dengan filter pencarian dan formulir pembuatan kegiatan.',
                'form_details' => [
                    ['field' => 'Nama Bidang (nama_bidang)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Wajib diisi dan harus unik.'],
                    ['field' => 'Detail Bidang (detail_bidang)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Wajib diisi.'],
                    ['field' => 'Urutan (urutan)', 'type' => 'Integer', 'required' => 'Ya', 'rules' => 'Minimal nilai 1', 'validation' => 'Digunakan untuk menentukan urutan visual di sidebar/menu.'],
                    ['field' => 'Slug (slug)', 'type' => 'String', 'required' => 'Tidak (Nullable)', 'rules' => 'Maksimal 255 karakter, harus unik', 'validation' => 'Otomatis digenerate dari nama bidang jika dikosongkan.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Masuk sebagai <strong>Admin</strong>, lalu buka menu <strong>Bidang Kerja</strong>.</li>
                    <li>Untuk membuat baru, isi nama bidang, keterangan singkat, dan urutan visual, lalu tekan <strong>Simpan</strong>.</li>
                    <li>Untuk mengubah atau menghapus, gunakan tombol aksi <strong>Edit</strong> atau <strong>Hapus</strong> pada baris data yang diinginkan.</li>
                </ol>',
                'sort_order' => 10,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Admin',
                'menu_name' => 'Kelola Jenis Kegiatan',
                'slug' => 'admin-kelola-kegiatan',
                'title' => 'Manajemen Jenis Kegiatan',
                'explanation' => 'Mengatur jenis kegiatan operasional yang dapat dipilih oleh Ketua Tim saat mendelegasikan tugas kepada anggota, beserta penanda kebutuhan dinas.',
                'route_target' => 'jenis-kegiatan.index',
                'roles_allowed' => ['Admin'],
                'output' => 'Daftar jenis kegiatan dengan klasifikasi kategori Utama/Tambahan serta aturan perjalanan dinas (DL/TL).',
                'form_details' => [
                    ['field' => 'Jenis Kegiatan (jenis_kegiatan)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Wajib diisi dan harus unik.'],
                    ['field' => 'Kategori (kategori)', 'type' => 'Enum / Dropdown', 'required' => 'Ya', 'rules' => 'Pilihan: Utama, Tambahan', 'validation' => 'Wajib diisi.'],
                    ['field' => 'Butuh DL/Translok (butuh_dl_atau_translok)', 'type' => 'Boolean / Toggle', 'required' => 'Ya', 'rules' => 'Pilihan: Ya (1), Tidak (0)', 'validation' => 'Menentukan apakah jenis kegiatan ini mewajibkan pengajuan dinas luar/transportasi lokal.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka halaman <strong>Jenis Kegiatan</strong> dari menu Admin.</li>
                    <li>Klik <strong>Tambah Jenis Kegiatan</strong> untuk memasukkan jenis tugas baru.</li>
                    <li>Tentukan kategori (Utama/Tambahan) dan status butuh perjalanan dinas (DL/TL).</li>
                    <li>Simpan perubahan. Perubahan akan langsung tersedia di formulir penugasan anggota.</li>
                </ol>',
                'sort_order' => 20,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Admin',
                'menu_name' => 'Kelola Pegawai & Role',
                'slug' => 'admin-kelola-pegawai',
                'title' => 'Manajemen Akun Pegawai & Multi-Role',
                'explanation' => 'Mengelola status keaktifan pegawai, hak akses (roles), serta konfigurasi multi-role untuk pegawai yang merangkap tugas.',
                'route_target' => 'pegawai-role.index',
                'roles_allowed' => ['Admin'],
                'output' => 'Akun pegawai aktif dengan konfigurasi peran struktural (Admin, Pimpinan, Ketua Tim, Anggota Tim).',
                'form_details' => [
                    ['field' => 'Nama Pegawai (nama_pegawai)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Wajib diisi sesuai nama lengkap SK.'],
                    ['field' => 'Username (username)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter, harus unik', 'validation' => 'Digunakan untuk keperluan login.'],
                    ['field' => 'Email (email)', 'type' => 'Email', 'required' => 'Tidak (Nullable)', 'rules' => 'Format email valid, maks 255 karakter, unik', 'validation' => 'Untuk keperluan notifikasi sistem.'],
                    ['field' => 'Jabatan (jabatan)', 'type' => 'String', 'required' => 'Tidak (Nullable)', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Sesuai jabatan resmi pegawai.'],
                    ['field' => 'Alamat (alamat)', 'type' => 'String', 'required' => 'Tidak (Nullable)', 'rules' => 'Maksimal 500 karakter', 'validation' => 'Alamat domisili pegawai.'],
                    ['field' => 'Password (password)', 'type' => 'Password', 'required' => 'Ya (saat baru)', 'rules' => 'Minimal 8 karakter', 'validation' => 'Dihash secara otomatis di backend.'],
                    ['field' => 'Konfirmasi Password (password_confirmation)', 'type' => 'Password', 'required' => 'Ya (saat baru)', 'rules' => 'Minimal 8 karakter, harus cocok dengan Password', 'validation' => 'Verifikasi keamanan password.'],
                    ['field' => 'Peran Pegawai (roles)', 'type' => 'Array of Integer / Checkbox Multi-Select', 'required' => 'Ya (min 1 role)', 'rules' => 'Pilihan: Admin, Pimpinan, Ketua Tim, Anggota Tim', 'validation' => 'Wajib diisi minimal satu role.'],
                    ['field' => 'Bulan Mulai Nonaktif (inactive_from_month)', 'type' => 'String / Date', 'required' => 'Ya (saat deaktif)', 'rules' => 'Format YYYY-MM (Bulan dan Tahun)', 'validation' => 'Wajib diisi saat menonaktifkan pegawai. Kosongkan jika ingin mengaktifkan kembali.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka menu <strong>Kelola Pegawai &amp; Role</strong>.</li>
                    <li>Gunakan tombol <strong>Tambah Pegawai</strong> untuk mendaftarkan akun baru.</li>
                    <li>Centang satu atau beberapa peran pada bagian role pegawai untuk memberikan hak akses ganda (multi-role).</li>
                    <li>Gunakan tombol <strong>Nonaktifkan</strong> jika ada pegawai mutasi/pensiun dengan mengisi bulan mulai nonaktif.</li>
                </ol>',
                'sort_order' => 30,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Admin',
                'menu_name' => 'Kelola Pengumuman',
                'slug' => 'admin-kelola-pengumuman',
                'title' => 'Manajemen Pengumuman Internal',
                'explanation' => 'Membuat dan mempublikasikan pengumuman penting sistem agar langsung tampil di halaman depan dashboard seluruh pegawai.',
                'route_target' => 'announcements.index',
                'roles_allowed' => ['Admin'],
                'output' => 'Banner pengumuman aktif di halaman dasbor seluruh pengguna.',
                'form_details' => [
                    ['field' => 'Judul Pengumuman (title)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Wajib diisi.'],
                    ['field' => 'Konten / Isi (content)', 'type' => 'Text', 'required' => 'Ya', 'rules' => 'Tidak terbatas', 'validation' => 'Mendukung format paragraf.'],
                    ['field' => 'Gambar Pengumuman (image)', 'type' => 'File / Image', 'required' => 'Ya (baru), Tidak (edit)', 'rules' => 'Mimes: jpeg, png, jpg, gif; Maksimal 5MB (5120 KB)', 'validation' => 'Hanya menerima file gambar banner.'],
                    ['field' => 'Tanggal Kadaluwarsa (end_date)', 'type' => 'Date', 'required' => 'Ya', 'rules' => 'Format YYYY-MM-DD, harus hari ini atau setelahnya', 'validation' => 'Batas waktu pengumuman tampil di dasbor.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka halaman <strong>Pengumuman</strong> di menu Admin.</li>
                    <li>Buat draf pengumuman baru dengan judul yang menarik dan isi pengumuman.</li>
                    <li>Set status aktif, lalu simpan. Pengumuman langsung terdistribusi ke seluruh dashboard pegawai secara real-time.</li>
                </ol>',
                'sort_order' => 40,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Admin',
                'menu_name' => 'Kelola Link Sidebar',
                'slug' => 'admin-kelola-sidebar-links',
                'title' => 'Manajemen Link Menu Sidebar',
                'explanation' => 'Mengatur menu eksternal dan grup tautan di bagian bawah sidebar (Informasi). Semua link otomatis terbuka di tab baru.',
                'route_target' => 'sidebar-links.index',
                'roles_allowed' => ['Admin'],
                'output' => 'Daftar link eksternal atau grup menu yang muncul di bagian bawah sidebar utama.',
                'form_details' => [
                    ['field' => 'Nama Link / Grup (name)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Wajib diisi.'],
                    ['field' => 'Tipe Link (type)', 'type' => 'Enum / Dropdown', 'required' => 'Ya', 'rules' => 'Pilihan: direct, group, sub', 'validation' => 'Direct untuk link langsung, group untuk grup yang memiliki sub-menu, sub untuk sub-menu di dalam group.'],
                    ['field' => 'Parent Group (parent_id)', 'type' => 'Integer / Dropdown', 'required' => 'Ya (jika Tipe = sub)', 'rules' => 'Harus berupa link bertipe group yang ada', 'validation' => 'Menghubungkan sub-link ke grup induk.'],
                    ['field' => 'URL (url)', 'type' => 'String', 'required' => 'Ya (jika Tipe = direct atau sub)', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Tautan tujuan url eksternal.'],
                    ['field' => 'Icon (icon)', 'type' => 'String', 'required' => 'Tidak (Nullable)', 'rules' => 'Maksimal 255 karakter (SVG/Heroicons)', 'validation' => 'Ikon visual menu.'],
                    ['field' => 'Urutan (sort_order)', 'type' => 'Integer', 'required' => 'Ya', 'rules' => 'Minimal 0', 'validation' => 'Mengatur posisi urutan link. Tautan lain dengan urutan sama atau lebih besar akan digeser otomatis.'],
                    ['field' => 'Is Special (is_special)', 'type' => 'Boolean / Checkbox', 'required' => 'Tidak', 'rules' => '0 atau 1', 'validation' => 'Menandakan jika tautan memerlukan sorotan khusus.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka menu <strong>Kelola Link Sidebar</strong> di bawah menu Admin.</li>
                    <li>Gunakan tombol <strong>Tambah Link Sidebar</strong> untuk mendaftarkan menu eksternal baru.</li>
                    <li>Tentukan tipe menu (Direct, Group, atau Sub) sesuai kebutuhan layout.</li>
                    <li>Isi parameter seperti nama, URL tujuan, ikon, dan nomor urutan tampil.</li>
                    <li>Klik <strong>Simpan</strong>. Menu eksternal akan langsung tampil di sidebar utama.</li>
                </ol>',
                'sort_order' => 45,
            ],

            // ==================== PIMPINAN ROLE ====================
            [
                'type' => 'user',
                'role_tab' => 'Pimpinan',
                'menu_name' => 'RK & IKI Pimpinan JPT',
                'slug' => 'pimpinan-rk-iki',
                'title' => 'Penyusunan Rencana Kinerja (RK) & Indikator Kinerja Individu (IKI) JPT',
                'explanation' => 'Menyusun sasaran kinerja strategis pimpinan yang nantinya akan di-cascade menjadi target bidang kerja dan kegiatan ketua tim.',
                'route_target' => 'rencana-indikator-jpt.rencana.index',
                'roles_allowed' => ['Pimpinan'],
                'output' => 'Cascading struktur RK JPT dan IKI JPT pimpinan.',
                'form_details' => [
                    ['field' => 'Tahun RK JPT (tahun)', 'type' => 'Integer', 'required' => 'Ya', 'rules' => 'Format tahun YYYY', 'validation' => 'Tahun anggaran rencana strategis JPT.'],
                    ['field' => 'Nama Rencana JPT (nama_rencana_jpt)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Wajib diisi.'],
                    ['field' => 'Nama Indikator JPT (nama_indikator_jpt)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Wajib diisi (dimasukkan di bawah rencana JPT terpilih).'],
                    ['field' => 'Satuan Target (satuan)', 'type' => 'String', 'required' => 'Tidak (Nullable)', 'rules' => 'Maksimal 100 karakter', 'validation' => 'Satuan output target IKI JPT.'],
                    ['field' => 'Target Kuantitas (target)', 'type' => 'Numeric', 'required' => 'Tidak (Nullable)', 'rules' => 'Minimal 0', 'validation' => 'Target volume kinerja IKI JPT.'],
                    ['field' => 'Realisasi Kuantitas (realisasi)', 'type' => 'Numeric', 'required' => 'Tidak (Nullable)', 'rules' => 'Minimal 0', 'validation' => 'Realisasi volume kinerja IKI JPT.'],
                    ['field' => 'Status Ketercapaian (status)', 'type' => 'Enum / Dropdown', 'required' => 'Tidak (Nullable)', 'rules' => 'Pilihan: Selesai, Belum Selesai', 'validation' => 'Menunjukkan apakah indikator sudah terpenuhi.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka menu <strong>Rencana Kinerja JPT</strong> di dashboard Pimpinan.</li>
                    <li>Klik <strong>Tambah Rencana</strong> untuk menginput RK baru.</li>
                    <li>Pada baris Rencana Kinerja yang sudah dibuat, klik opsi <strong>Tambah Indikator (IKI JPT)</strong> untuk memasukkan indikator turunannya.</li>
                </ol>',
                'sort_order' => 50,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Pimpinan',
                'menu_name' => 'Agenda Pimpinan',
                'slug' => 'pimpinan-agenda',
                'title' => 'Manajemen Agenda & CKP Pimpinan',
                'explanation' => 'Mengelola agenda kegiatan pimpinan sehari-hari dan merubahnya secara otomatis menjadi pelaporan CKP Pimpinan bulanan.',
                'route_target' => 'agenda.index',
                'roles_allowed' => ['Pimpinan'],
                'output' => 'Daftar agenda kegiatan pimpinan yang terintegrasi dengan pelaporan CKP.',
                'form_details' => [
                    ['field' => 'Nama Agenda (nama_agenda)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Tidak terbatas', 'validation' => 'Judul/perihal agenda rapat/dinas.'],
                    ['field' => 'Tanggal Mulai (tanggal_mulai)', 'type' => 'Date', 'required' => 'Ya', 'rules' => 'Format YYYY-MM-DD', 'validation' => 'Tanggal pelaksanaan dimulai.'],
                    ['field' => 'Tanggal Selesai (tanggal_selesai)', 'type' => 'Date', 'required' => 'Ya', 'rules' => 'Format YYYY-MM-DD, harus setelah atau sama dengan Tanggal Mulai', 'validation' => 'Tanggal pelaksanaan berakhir.'],
                    ['field' => 'Rencana Kerja JPT (rk_jpt)', 'type' => 'Integer / Dropdown', 'required' => 'Ya', 'rules' => 'Harus terdaftar di rencana_jpts', 'validation' => 'Menghubungkan target ke Rencana Strategis Pimpinan.'],
                    ['field' => 'Indikator JPT (iki_jpt)', 'type' => 'Integer / Dropdown', 'required' => 'Ya', 'rules' => 'Harus terdaftar di indikator_jpts di bawah RK terpilih', 'validation' => 'Menghubungkan target ke IKI Pimpinan.'],
                    ['field' => 'Target Kuantitas (target)', 'type' => 'Integer', 'required' => 'Tidak (Nullable)', 'rules' => 'Minimal 1, default 1', 'validation' => 'Kuantitas target agenda.'],
                    ['field' => 'Satuan Target (satuan_target)', 'type' => 'String', 'required' => 'Tidak (Nullable)', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Satuan output agenda.'],
                    ['field' => 'Realisasi Kuantitas (realisasi)', 'type' => 'Integer', 'required' => 'Tidak (Nullable)', 'rules' => 'Maksimal sebesar target', 'validation' => 'Realisasi kuantitas yang dicapai.'],
                    ['field' => 'Link Bukti Realisasi (link_bukti)', 'type' => 'String (URL)', 'required' => 'Tidak (Nullable)', 'rules' => 'Format URL valid', 'validation' => 'Tautan bukti dukung pelaksanaan agenda.'],
                    ['field' => 'Status Agenda (status)', 'type' => 'Enum / Dropdown', 'required' => 'Ya', 'rules' => 'Pilihan: Selesai, Belum Selesai', 'validation' => 'Status pelaksanaan agenda.'],
                    ['field' => 'Butuh Dinas Luar (butuh_dl)', 'type' => 'Boolean / Checkbox', 'required' => 'Tidak (Nullable)', 'rules' => 'Pilihan: 1 (Ya), 0 (Tidak)', 'validation' => 'Menandakan apakah agenda memerlukan dinas luar.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka menu <strong>Agenda Pimpinan</strong>.</li>
                    <li>Tambahkan agenda baru setiap ada rencana rapat, kunker, atau tugas luar pimpinan.</li>
                    <li>Setelah agenda terlaksana, klik tombol <strong>Jadikan CKP</strong> pada bulan pelaksanaan terkait untuk mengkonversinya langsung ke CKP bulanan Anda.</li>
                </ol>',
                'sort_order' => 60,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Pimpinan',
                'menu_name' => 'Persetujuan Dinas Luar',
                'slug' => 'pimpinan-acc-dl',
                'title' => 'Persetujuan Dinas Luar & Translok',
                'explanation' => 'Meninjau dan memutuskan (ACC/Tolak) pengajuan perjalanan dinas luar (DL) atau transportasi lokal (Translok) dari penugasan anggota tim.',
                'route_target' => 'master-kegiatan.index_rk_dl',
                'roles_allowed' => ['Pimpinan'],
                'output' => 'Persetujuan perjalanan dinas yang mengubah status kalender perjalanan dinas menjadi aktif.',
                'form_details' => [
                    ['field' => 'Status Dinas Luar / Translok (status_dl / status_translok)', 'type' => 'Enum / Button Action', 'required' => 'Ya', 'rules' => 'Pilihan: ACC (Approved) atau Tolak (Rejected)', 'validation' => 'Persetujuan bersifat final dan merubah status penugasan secara real-time.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka menu <strong>Rencana Kerja Perlu DL/Translok</strong>.</li>
                    <li>Periksa daftar nama pegawai, tanggal dinas, serta jenis penugasan kerja yang diajukan.</li>
                    <li>Klik <strong>ACC</strong> untuk menyetujui. Tugas otomatis dilabeli status "DL" dan masuk ke kalender dinas.</li>
                    <li>Klik <strong>Tolak</strong> dan masukkan alasan jika pengajuan tidak disetujui untuk dikembalikan ke Ketua Tim.</li>
                </ol>',
                'sort_order' => 70,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Pimpinan',
                'menu_name' => 'Kirim Push Reminder',
                'slug' => 'pimpinan-push-reminder',
                'title' => 'Kirim Notifikasi Pengingat Tugas Pegawai',
                'explanation' => 'Mengirimkan notifikasi web pengingat (Push Notification) secara langsung ke perangkat pegawai yang memiliki tumpukan tugas terlewat/deadline dekat.',
                'route_target' => 'dashboard',
                'roles_allowed' => ['Pimpinan'],
                'output' => 'Notifikasi real-time ke browser/HP pegawai terkait.',
                'form_details' => [
                    ['field' => 'Kirim Pengingat (Button)', 'type' => 'Direct Action Button', 'required' => 'Tidak ada form modal', 'rules' => 'Sekali klik di baris pegawai terkait', 'validation' => 'Mengirimkan push notification instan ke browser pegawai yang terlambat.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka halaman utama <strong>Dashboard Pimpinan</strong>.</li>
                    <li>Scroll ke tabel <strong>Rekap Penugasan Pegawai</strong>.</li>
                    <li>Cari nama pegawai yang memiliki tugas berstatus "Terlewat" (belum dikirim hasil tapi lewat tenggat waktu).</li>
                    <li>Klik tombol **Kirim Pengingat** di kolom aksi. Sistem akan mengirim notifikasi web instan ke gawai pegawai bersangkutan.</li>
                </ol>',
                'sort_order' => 80,
            ],

            // ==================== KETUA TIM ROLE ====================
            [
                'type' => 'user',
                'role_tab' => 'Ketua Tim',
                'menu_name' => '1. Buat Kegiatan Baru',
                'slug' => 'ketua-buat-kegiatan-baru',
                'title' => 'Membuat Kegiatan Baru',
                'explanation' => 'Menyusun Rencana Kegiatan utama di bawah bidang koordinasi Ketua Tim. Setiap kegiatan wajib dihubungkan dengan sasaran Rencana Kinerja (RK) & Indikator Kinerja Individu (IKI) JPT Pimpinan guna memastikan proses cascading berjalan terarah.',
                'route_target' => 'kegiatan.index',
                'roles_allowed' => ['Ketua Tim', 'Admin'],
                'output' => 'Data Kegiatan Utama baru yang terdaftar di bawah bidang fungsi terkait.',
                'form_details' => [
                    ['field' => 'Bidang Kerja (id_bidang)', 'type' => 'Integer / Dropdown', 'required' => 'Ya', 'rules' => 'Harus terdaftar di tabel bidang', 'validation' => 'Menentukan bidang kerja penanggung jawab.'],
                    ['field' => 'Nama Kegiatan (nama_rk_kegiatan)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Nama program kerja / kegiatan utama.'],
                    ['field' => 'Rencana Kinerja (RK) JPT Pimpinan (rk_jpt)', 'type' => 'Integer / Dropdown', 'required' => 'Ya', 'rules' => 'Harus terdaftar di rencana_jpts', 'validation' => 'Menghubungkan target ke Pimpinan.'],
                    ['field' => 'Indikator Kinerja Individu (IKI) JPT Pimpinan (iki_jpt)', 'type' => 'Integer / Dropdown', 'required' => 'Ya', 'rules' => 'Harus terdaftar di indikator_jpts di bawah RK JPT terpilih', 'validation' => 'Wajib diisi.'],
                    ['field' => 'Penanggung Jawab / Ketua Tim (id_penanggung_jawab)', 'type' => 'Integer / Dropdown', 'required' => 'Ya', 'rules' => 'Pegawai aktif dengan role Ketua Tim. Terkunci jika Ketua Tim login, dapat dipilih jika Admin login', 'validation' => 'Identitas penanggung jawab kegiatan.'],
                    ['field' => 'Tahun Kegiatan (tahun_kegiatan)', 'type' => 'Integer', 'required' => 'Ya', 'rules' => 'Format YYYY', 'validation' => 'Tahun anggaran program kerja berjalan.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka menu <strong>Rencana Kinerja -> Rencana Kerja Per Fungsi</strong>.</li>
                    <li>Klik tombol **Tambah Kegiatan** di kanan atas.</li>
                    <li>Isi formulir pembuatan kegiatan seperti Nama Kegiatan, Tahun, dan pilih RK JPT serta IKI JPT Pimpinan.</li>
                    <li>Klik **Simpan**. Kegiatan baru akan terdaftar dan siap dibuatkan sub-kegiatannya.</li>
                </ol>',
                'sort_order' => 90,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Ketua Tim',
                'menu_name' => '2. Buat Sub kegiatan Baru',
                'slug' => 'ketua-buat-sub-kegiatan',
                'title' => 'Membuat Sub-Kegiatan Baru',
                'explanation' => 'Merinci kegiatan utama menjadi unit-unit pekerjaan taktis/sub-kegiatan operasional bulanan atau periodik, lengkap dengan volume target dan rentang waktu pelaksanaan.',
                'route_target' => 'kegiatan.index',
                'roles_allowed' => ['Ketua Tim', 'Admin'],
                'output' => 'Sub-kegiatan baru yang dikaitkan di bawah payung kegiatan utama.',
                'form_details' => [
                    ['field' => 'Nama Sub-Kegiatan (nama_sub_kegiatan)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Rincian taktis operasional kegiatan.'],
                    ['field' => 'Target Kuantitas (target)', 'type' => 'Integer', 'required' => 'Ya', 'rules' => 'Minimal nilai 1', 'validation' => 'Target volume total output sub-kegiatan.'],
                    ['field' => 'Satuan Target (satuan_target)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter (misal: Laporan, Dokumen, Sampel)', 'validation' => 'Satuan output target.'],
                    ['field' => 'Tanggal Mulai (tanggal_mulai)', 'type' => 'Date', 'required' => 'Ya', 'rules' => 'Format YYYY-MM-DD', 'validation' => 'Tanggal awal sub-kegiatan dimulai.'],
                    ['field' => 'Tanggal Selesai (tanggal_selesai)', 'type' => 'Date', 'required' => 'Ya', 'rules' => 'Format YYYY-MM-DD, harus setelah atau sama dengan Tanggal Mulai', 'validation' => 'Batas akhir sub-kegiatan selesai.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka halaman **Rencana Kerja Per Fungsi** dan cari kegiatan utama yang dituju.</li>
                    <li>Klik tombol **Sub Kegiatan** di baris kegiatan tersebut.</li>
                    <li>Klik **Tambah Sub-Kegiatan** lalu isi nama sub-kegiatan, total target, satuan output, dan tanggal mulai/selesai.</li>
                    <li>Klik **Simpan**. Sub-kegiatan kini siap didelegasikan ke anggota.</li>
                </ol>',
                'sort_order' => 100,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Ketua Tim',
                'menu_name' => '3. Buat Penugasan Baru',
                'slug' => 'ketua-buat-penugasan',
                'title' => 'Mendelegasikan Penugasan Anggota (DL/Translok vs Tanpa DL)',
                'explanation' => 'Membagi beban kerja sub-kegiatan kepada anggota tim. Terdapat dua jenis penugasan:<br>1. **Penugasan Memerlukan Perjalanan Dinas (DL/Translok):** Memerlukan pengajuan dan persetujuan (ACC) dari Pimpinan. Anggota tidak bisa mengirim laporan sebelum disetujui.<br>2. **Penugasan Biasa (Tanpa DL):** Anggota dapat langsung mengerjakan dan mengirimkan laporan pekerjaan tanpa menunggu persetujuan.',
                'route_target' => 'kegiatan.index',
                'roles_allowed' => ['Ketua Tim'],
                'output' => 'Delegasi tugas baru di Todo List anggota serta pencatatan agenda dinas.',
                'form_details' => [
                    ['field' => 'Anggota Tim (id_anggota)', 'type' => 'Integer / Dropdown', 'required' => 'Ya', 'rules' => 'Pegawai aktif', 'validation' => 'Pegawai yang menerima penugasan.'],
                    ['field' => 'Jenis Kegiatan (id_jenis_kegiatan)', 'type' => 'Integer / Dropdown', 'required' => 'Ya', 'rules' => 'Terdaftar di jenis_kegiatans atau LAINNYA', 'validation' => 'Tipe pekerjaan yang didelegasikan.'],
                    ['field' => 'Jenis Kegiatan Baru (jenis_kegiatan_baru)', 'type' => 'String', 'required' => 'Ya (jika Jenis Kegiatan = LAINNYA)', 'rules' => 'Maksimal 100 karakter', 'validation' => 'Diisi untuk membuat jenis kegiatan baru.'],
                    ['field' => 'Target Anggota (target)', 'type' => 'Integer', 'required' => 'Ya', 'rules' => 'Minimal 1. Jika Jenis Kegiatan bertipe Pengawasan / Supervisi / Perjalanan Dinas, target otomatis dikunci ke angka 1', 'validation' => 'Volume target untuk anggota.'],
                    ['field' => 'Satuan Target (satuan_target)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 50 karakter', 'validation' => 'Satuan output target.'],
                    ['field' => 'Butuh DL (butuh_dl)', 'type' => 'Boolean / Checkbox', 'required' => 'Tidak (Nullable)', 'rules' => 'Pilihan: 1 (Ya), 0 (Tidak). DL & Translok tidak boleh aktif bersamaan', 'validation' => 'Jika diaktifkan, memerlukan persetujuan Pimpinan sebelum tugas dapat dikerjakan.'],
                    ['field' => 'Butuh Translok (butuh_translok)', 'type' => 'Boolean / Checkbox', 'required' => 'Tidak (Nullable)', 'rules' => 'Pilihan: 1 (Ya), 0 (Tidak). DL & Translok tidak boleh aktif bersamaan', 'validation' => 'Jika diaktifkan, memerlukan persetujuan Pimpinan sebelum tugas dapat dikerjakan.'],
                    ['field' => 'Tanggal Mulai Utama (tanggal_mulai)', 'type' => 'Date', 'required' => 'Ya', 'rules' => 'Format YYYY-MM-DD, harus berada dalam rentang tanggal sub-kegiatan', 'validation' => 'Tanggal awal penugasan.'],
                    ['field' => 'Tanggal Selesai Utama (tanggal_selesai)', 'type' => 'Date', 'required' => 'Ya', 'rules' => 'Format YYYY-MM-DD, harus setelah atau sama dengan Tanggal Mulai, dan di dalam rentang sub-kegiatan', 'validation' => 'Tanggal akhir penugasan.'],
                    ['field' => 'Tanggal Mulai Tambahan (tanggal_mulai_list[])', 'type' => 'Array of Dates', 'required' => 'Tidak (Nullable)', 'rules' => 'Boleh kosong. Jika diisi, harus berupa tanggal valid di dalam rentang sub-kegiatan', 'validation' => 'Tanggal mulai tugas tambahan.'],
                    ['field' => 'Tanggal Selesai Tambahan (tanggal_selesai_list[])', 'type' => 'Array of Dates', 'required' => 'Tidak (Nullable)', 'rules' => 'Boleh kosong. Jika diisi, harus setelah atau sama dengan tanggal_mulai_list pasangan, dan di dalam rentang sub-kegiatan', 'validation' => 'Tanggal selesai tugas tambahan.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka sub-kegiatan yang ditargetkan di halaman Rencana Kerja.</li>
                    <li>Klik opsi delegasi tugas ke anggota (Tambah Penugasan).</li>
                    <li>Pilih nama anggota, jenis kegiatan, target kuantitas porsi dia, dan rentang tanggal kerja.</li>
                    <li>Jika jenis kegiatannya butuh dinas lapangan, beri tanda centang pada opsi DL atau Translok.</li>
                    <li>Klik **Simpan**. Jika butuh dinas, sistem otomatis meneruskan ajuan tersebut ke Pimpinan.</li>
                </ol>',
                'sort_order' => 110,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Ketua Tim',
                'menu_name' => '4. Terima Pengiriman Anggota',
                'slug' => 'ketua-terima-pengiriman',
                'title' => 'Verifikasi, Penilaian, & Revisi Laporan Anggota',
                'explanation' => 'Memeriksa bukti pengerjaan tugas dari anggota. Ketua Tim wajib mengevaluasi setiap berkas pengiriman. Jika sesuai, berikan rating bintang (1-5★). Jika salah, berikan status **Revisi** beserta catatan perbaikan. Proses ini harus terus dilakukan setiap ada pengiriman baru dan baru dianggap selesai jika status penerimaan terakhir adalah **Diterima**.',
                'route_target' => 'kegiatan.index',
                'roles_allowed' => ['Ketua Tim'],
                'output' => 'Persetujuan laporan pengiriman dengan nilai rating kualitas atau pengembalian revisi.',
                'form_details' => [
                    ['field' => 'Tanggal Pemeriksaan (tanggal_penerimaan)', 'type' => 'Date', 'required' => 'Ya', 'rules' => 'Format YYYY-MM-DD. Terkunci otomatis ke hari ini jika tugas dimulai setelah 31 Maret 2026', 'validation' => 'Tanggal verifikasi laporan oleh Ketua Tim.'],
                    ['field' => 'Jumlah Diterima (jumlah_diterima)', 'type' => 'Integer', 'required' => 'Ya', 'rules' => 'Minimal 1, maksimal tidak boleh melebihi jumlah yang dikirim oleh anggota', 'validation' => 'Volume pekerjaan yang disetujui.'],
                    ['field' => 'Status Tindakan (status)', 'type' => 'Enum / Dropdown', 'required' => 'Ya', 'rules' => 'Pilihan: Diterima, Revisi', 'validation' => 'Menentukan apakah pengiriman disetujui atau dikembalikan untuk diperbaiki.'],
                    ['field' => 'Catatan Evaluasi (catatan)', 'type' => 'String', 'required' => 'Ya (jika status = Revisi)', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Wajib diisi sebagai panduan revisi bagi anggota jika statusnya Revisi.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka menu **Tagihan Kerja**, periksa daftar tugas berlabel "Perlu Diperiksa" (ikon kuning).</li>
                    <li>Klik tugas tersebut untuk mengunduh/memeriksa tautan bukti dukung yang dikirim anggota.</li>
                    <li>Klik **Terima** lalu pilih jumlah rating bintang kualitas jika pengiriman disetujui.</li>
                    <li>Klik **Revisi** dan isi catatan koreksi jika laporan perlu diperbaiki oleh anggota. Penerimaan harus terus diulang setiap ada pengiriman baru dan selesai saat statusnya Diterima.</li>
                </ol>',
                'sort_order' => 120,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Ketua Tim',
                'menu_name' => '5. Konversi CKP Ketua Tim',
                'slug' => 'ketua-konversi-ckp',
                'title' => 'Menjadikan Rencana Kerja sebagai CKP Ketua Tim',
                'explanation' => 'Mengonversi Sub Kegiatan yang telah berjalan (berdasarkan persentase pencapaian pengiriman anggota tim) menjadi capaian kinerja CKP Utama Ketua Tim untuk bulan pelaporan tertentu.',
                'route_target' => 'ckp.pegawai.index',
                'roles_allowed' => ['Ketua Tim'],
                'output' => 'Baris uraian kegiatan baru terdaftar di halaman CKP bulanan Ketua Tim.',
                'form_details' => [
                    ['field' => 'Uraian Kegiatan CKP (uraian)', 'type' => 'Text', 'required' => 'Ya', 'rules' => 'Tidak terbatas', 'validation' => 'Uraian deskripsi CKP yang akan dicatat.'],
                    ['field' => 'Bulan Pelaporan CKP (bulan_ckp)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Format YYYY-MM. Hanya bulan yang belum memiliki CKP dan bernilai sebelum bulan berjalan. Dan untuk bulan terakhir penugasan, hanya bisa dipilih jika progres sub-kegiatan sudah mencapai 100%', 'validation' => 'Bulan target pelaporan CKP.'],
                    ['field' => 'Keterangan Tambahan (keterangan)', 'type' => 'Text', 'required' => 'Tidak (Nullable)', 'rules' => 'Tidak terbatas', 'validation' => 'Keterangan opsional tambahan.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka halaman **Tagihan Kerja** Ketua Tim.</li>
                    <li>Pada sub-kegiatan yang progresnya sudah berjalan (berdasarkan persentase sub-kegiatan), klik tombol **Buat CKP** pada bulan yang bersesuaian.</li>
                    <li>Buka menu **CKP Saya** untuk melihat dan mengekspor kompilasi laporan CKP Ketua Tim ke Excel.</li>
                </ol>',
                'sort_order' => 130,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Ketua Tim',
                'menu_name' => '6. Transfer Kegiatan',
                'slug' => 'ketua-transfer-kegiatan-tim',
                'title' => 'Mentransfer Kepemilikan Kegiatan Kerja',
                'explanation' => 'Mengalihkan tanggung jawab pengelolaan kegiatan beserta sub-kegiatan dan penugasan di dalamnya kepada Ketua Tim lain (misal akibat mutasi atau perubahan struktur fungsi kerja).',
                'route_target' => 'kegiatan.index',
                'roles_allowed' => ['Ketua Tim', 'Admin'],
                'output' => 'Perpindahan hak kelola kegiatan kerja per tanggal pencatatan `transferred_at`.',
                'form_details' => [
                    ['field' => 'Ketua Tim Penerima (to_ketua_id)', 'type' => 'Integer / Dropdown', 'required' => 'Ya', 'rules' => 'Pegawai aktif dengan role Ketua Tim, tidak boleh mentransfer ke diri sendiri', 'validation' => 'Penerima hak kelola kegiatan.'],
                    ['field' => 'Tanggal Transfer (transferred_at)', 'type' => 'Date', 'required' => 'Ya', 'rules' => 'Format YYYY-MM-DD', 'validation' => 'Tanggal efektif perpindahan kegiatan.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka halaman **Rencana Kerja Per Fungsi**.</li>
                    <li>Cari kegiatan yang akan dipindahkan, lalu klik tombol **Transfer**.</li>
                    <li>Pilih Ketua Tim penerima tanggung jawab yang baru, klik **Konfirmasi**.</li>
                </ol>',
                'sort_order' => 140,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Ketua Tim',
                'menu_name' => '7. Edit & Hapus Data',
                'slug' => 'ketua-edit-hapus',
                'title' => 'Aturan Modifikasi & Penghapusan Data Rencana Kerja',
                'explanation' => 'Ketua Tim memiliki wewenang untuk melakukan pengeditan atau penghapusan data Kegiatan, Sub Kegiatan, dan Penugasan anggota dengan batasan validasi ketat sistem untuk menjaga integritas data.<br><br>**Batasan Penting:**<br>1. **Kegiatan:** Tidak dapat dihapus jika di dalamnya terdapat sub-kegiatan/penugasan yang sudah memiliki riwayat pengiriman diterima (kegiatan berjalan).<br>2. **Penugasan:** Tidak dapat diedit/dihapus jika penugasan dinas (DL/TL) sudah disetujui dan dijadikan CKP oleh anggota tim.',
                'route_target' => 'kegiatan.index',
                'roles_allowed' => ['Ketua Tim', 'Admin'],
                'output' => 'Pembalasan/pembaruan data atau penghapusan record di database.',
                'form_details' => [
                    ['field' => 'Aksi Edit / Hapus (Button)', 'type' => 'Action Confirmation', 'required' => 'Ya', 'rules' => 'Validasi bisnis ketat', 'validation' => 'Sistem menolak penghapusan kegiatan jika terdapat transaksi pengiriman dari anggota yang telah diterima.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Untuk mengedit Kegiatan, klik tombol **Edit** pada baris kegiatan, ubah detail form, lalu simpan.</li>
                    <li>Untuk menghapus, gunakan tombol **Hapus** (ikon tempat sampah). Jika sistem mendeteksi data sudah memiliki relasi transaksi berjalan, tombol hapus akan terkunci secara otomatis.</li>
                </ol>',
                'sort_order' => 150,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Ketua Tim',
                'menu_name' => '8. Nilai & Ranking Ketua Tim',
                'slug' => 'ketua-rating-nilai',
                'title' => 'Logika Perhitungan & Perankingan Ketua Tim',
                'explanation' => 'Penilaian kinerja Ketua Tim dihitung murni dari nilai **F5 (Kinerja Pengawasan)** terhadap seluruh anggota tim, disesuaikan dengan koefisien fairness berdasarkan volume beban kerja tim.',
                'route_target' => 'dashboard',
                'roles_allowed' => ['Ketua Tim', 'Pimpinan', 'Admin'],
                'output' => 'Daftar peringkat Ketua Tim di halaman peringkat kantor.',
                'form_details' => [
                    ['field' => 'Skor Pengawasan (F5) & Fairness Beban Kerja Tim', 'type' => 'Kalkulasi Sistem', 'required' => 'Tidak ada form', 'rules' => 'F5 diambil 100% sebagai skor dasar, lalu dikalikan koefisien beban tim (0.85 s.d 1.15)', 'validation' => 'Koefisien dihitung dari rasio total target tim terhadap rata-rata target tim seluruh Katim di kantor.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Pastikan seluruh anggota tim melaporkan tugas mereka tepat waktu dan berikan rating bintang kualitas terbaik (5★) untuk menjaga skor F5 maksimal.</li>
                    <li>Katim yang memimpin tim dengan total target di atas rata-rata kantor akan menerima bonus performa hingga +15% (koefisien 1.15). Sebaliknya, total target di bawah rata-rata mendapat penyesuaian (koefisien minimal 0.85).</li>
                </ol>',
                'sort_order' => 155,
            ],

            // ==================== ANGGOTA TIM ROLE ====================
            [
                'type' => 'user',
                'role_tab' => 'Anggota Tim',
                'menu_name' => '1. Pantau & Kerjakan Tugas',
                'slug' => 'anggota-pantau-todo',
                'title' => 'Melihat & Memantau Penugasan Kerja',
                'explanation' => 'Memantau Todo List pada Dashboard untuk melihat daftar tugas yang didelegasikan oleh Ketua Tim pada bidang tempat Anda ditugaskan. Tugas dibagi dalam kategori Berjalan, Terlewat, dan Revisi.',
                'route_target' => 'dashboard',
                'roles_allowed' => ['Anggota Tim', 'Ketua Tim', 'Pimpinan', 'Admin'],
                'output' => 'Daftar penugasan siap kerja.',
                'form_details' => [
                    ['field' => 'Daftar Tugas (Dashboard)', 'type' => 'Visual Display only', 'required' => 'Tidak ada form modal', 'rules' => 'Menampilkan daftar tugas berjalan, revisi, dan terlewat', 'validation' => 'Tidak memerlukan parameter input.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka menu **Dashboard** utama setelah login.</li>
                    <li>Lihat bagian **Todo List Anggota** untuk memantau prioritas pengerjaan tugas Anda di bidang yang diassign.</li>
                    <li>Kerjakan tugas-tugas lapangan/kantor sesuai deskripsi penugasan yang tercantum.</li>
                </ol>',
                'sort_order' => 160,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Anggota Tim',
                'menu_name' => '2. Kirim Laporan Tugas',
                'slug' => 'anggota-kirim-laporan',
                'title' => 'Mengirim Laporan Realisasi & Bukti Pengerjaan (Termasuk Mekanisme Lintas Bulan)',
                'explanation' => 'Melaporkan hasil pengerjaan tugas kepada Ketua Tim dengan menyertakan tautan berkas bukti dukung yang valid agar dapat diperiksa. Sistem mendukung pengiriman lintas bulan di mana Anggota Tim diperbolehkan mencicil/melunasi laporan tugas di bulan-bulan berbeda selama masa aktif penugasan dengan memilih Bulan Pengiriman yang sesuai.',
                'route_target' => 'dashboard',
                'roles_allowed' => ['Anggota Tim'],
                'output' => 'Histori pengiriman baru dengan status "Perlu Diperiksa" yang terbagi rapi berdasarkan bulan target masing-masing di CKP.',
                'form_details' => [
                    ['field' => 'Tanggal Pengiriman (tanggal_pengiriman)', 'type' => 'Date', 'required' => 'Ya', 'rules' => 'Format YYYY-MM-DD. Terkunci otomatis ke hari ini jika tugas dimulai setelah 31 Maret 2026', 'validation' => 'Batas pengisian tanggal submit.'],
                    ['field' => 'Bulan Realisasi CKP (bulan_pengiriman)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Format YYYY-MM. Hanya bulan aktif dalam rentang penugasan, tidak boleh bulan belum tiba, dan belum memiliki pengiriman berstatus Diterima', 'validation' => 'Mekanisme ini memungkinkan Anda memilih bulan target pelaporan CKP.'],
                    ['field' => 'Tipe Pengiriman (tipe_pengiriman)', 'type' => 'Enum / Dropdown', 'required' => 'Ya', 'rules' => 'Pilihan: Cicilan, Pelunasan. Otomatis Pelunasan jika penugasan hanya 1 bulan atau bulan terakhir', 'validation' => 'Wajib dipilih.'],
                    ['field' => 'Volume Realisasi (jumlah_dikirim)', 'type' => 'Integer', 'required' => 'Ya', 'rules' => 'Minimal 1, harus diisi secara AKUMULATIF (total kumulatif target akhir), maksimal tidak melebihi target', 'validation' => 'Volume hasil kerja yang dikirim.'],
                    ['field' => 'Media Pengiriman (media_pengiriman)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Jenis media pengiriman (misal: File, Email, Tautan).'],
                    ['field' => 'Bukti Dukung Link (bukti_dukung)', 'type' => 'String (URL)', 'required' => 'Ya', 'rules' => 'Format URL valid, maksimal 255 karakter', 'validation' => 'Tautan Google Drive / berkas output.'],
                    ['field' => 'Catatan Tambahan (catatan)', 'type' => 'String', 'required' => 'Tidak (Nullable)', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Catatan penjelasan opsional.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka menu **Tagihan Kerja**, cari penugasan Anda.</li>
                    <li>Klik tombol **Buat Pengiriman** (tombol dinonaktifkan jika tugas butuh DL tapi belum di-ACC Pimpinan).</li>
                    <li>Pilih <strong>Bulan Pengiriman / Realisasi</strong> target (misal: "2026-05" untuk Mei, "2026-06" untuk Juni) untuk mekanisme pengiriman lintas bulan.</li>
                    <li>Pilih tipe laporan (pilih <strong>Cicilan</strong> untuk bulan-bulan awal, atau <strong>Pelunasan</strong> untuk bulan penutupan penugasan).</li>
                    <li>Isi jumlah volume realisasi secara akumulatif, dan tautkan link bukti pengiriman serta media pengiriman.</li>
                    <li>Klik **Kirim**. Status pengiriman akan berubah menjadi "Perlu Diperiksa".</li>
                </ol>',
                'sort_order' => 170,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Anggota Tim',
                'menu_name' => '3. Kirim Ulang Laporan Revisi',
                'slug' => 'anggota-revisi-laporan',
                'title' => 'Mengirim Ulang Laporan yang Direvisi',
                'explanation' => 'Apabila laporan pengiriman Anda ditolak (status **Revisi**) oleh Ketua Tim, Anda wajib melakukan perbaikan berkas berdasarkan catatan koreksi yang diberikan dan melakukan submit ulang.',
                'route_target' => 'dashboard',
                'roles_allowed' => ['Anggota Tim'],
                'output' => 'Data pengiriman diperbarui untuk diperiksa ulang oleh Ketua Tim.',
                'form_details' => [
                    ['field' => 'Tanggal Pengiriman (tanggal_pengiriman)', 'type' => 'Date', 'required' => 'Ya', 'rules' => 'Format YYYY-MM-DD. Terkunci otomatis ke hari ini jika tugas dimulai setelah 31 Maret 2026', 'validation' => 'Tanggal kirim ulang revisi.'],
                    ['field' => 'Bulan Realisasi CKP (bulan_pengiriman)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Format YYYY-MM. Harus sama dengan bulan penugasan yang sedang direvisi', 'validation' => 'Bulan target pelaporan CKP.'],
                    ['field' => 'Tipe Pengiriman (tipe_pengiriman)', 'type' => 'Enum / Dropdown', 'required' => 'Ya', 'rules' => 'Pilihan: Cicilan, Pelunasan', 'validation' => 'Wajib dipilih.'],
                    ['field' => 'Volume Realisasi Baru (jumlah_dikirim)', 'type' => 'Integer', 'required' => 'Ya', 'rules' => 'Minimal 1, harus diisi secara AKUMULATIF', 'validation' => 'Volume realisasi baru.'],
                    ['field' => 'Media Pengiriman (media_pengiriman)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Jenis media pengiriman.'],
                    ['field' => 'Bukti Dukung Link Baru (bukti_dukung)', 'type' => 'String (URL)', 'required' => 'Ya', 'rules' => 'Format URL valid, maksimal 255 karakter', 'validation' => 'Link bukti dukung yang sudah diperbaiki.'],
                    ['field' => 'Catatan Tambahan (catatan)', 'type' => 'String', 'required' => 'Tidak (Nullable)', 'rules' => 'Maksimal 255 karakter', 'validation' => 'Catatan penjelasan perbaikan.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Periksa catatan Ketua Tim pada histori pengiriman tugas di dashboard/halaman detail tugas.</li>
                    <li>Lakukan perbaikan berkas laporan Anda.</li>
                    <li>Klik tombol pengiriman ulang, masukkan jumlah volume dan tautan berkas revisi yang baru, lalu kirim.</li>
                </ol>',
                'sort_order' => 180,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Anggota Tim',
                'menu_name' => '4. Konversi CKP Anggota',
                'slug' => 'anggota-konversi-ckp-tim',
                'title' => 'Menjadikan Penugasan sebagai CKP Saya',
                'explanation' => 'Mengonversi penugasan yang telah disetujui (status penerimaan **Diterima**) menjadi baris laporan CKP bulanan Anggota Tim.',
                'route_target' => 'ckp.pegawai.index',
                'roles_allowed' => ['Anggota Tim'],
                'output' => 'Penambahan baris uraian tugas baru di laporan CKP bulanan pegawai.',
                'form_details' => [
                    ['field' => 'Uraian Kegiatan CKP (uraian)', 'type' => 'Text', 'required' => 'Ya', 'rules' => 'Tidak terbatas', 'validation' => 'Uraian deskripsi CKP Anggota.'],
                    ['field' => 'Bulan Pelaporan CKP (bulan_ckp)', 'type' => 'String', 'required' => 'Ya', 'rules' => 'Format YYYY-MM. Hanya bulan yang memiliki pengiriman berstatus Diterima yang belum masuk CKP', 'validation' => 'Bulan target pelaporan CKP.'],
                    ['field' => 'Keterangan Tambahan (keterangan)', 'type' => 'Text', 'required' => 'Tidak (Nullable)', 'rules' => 'Tidak terbatas', 'validation' => 'Keterangan opsional tambahan.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka halaman **CKP Saya**.</li>
                    <li>Di bagian daftar penugasan disetujui, klik tombol **Jadikan CKP** pada bulan target pelaporan yang dituju.</li>
                    <li>Ubah deskripsi uraian CKP menjadi bahasa laporan formal jika dirasa perlu, lalu ekspor laporan ke Excel.</li>
                </ol>',
                'sort_order' => 190,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Anggota Tim',
                'menu_name' => '5. Nilai & Rating Kinerja',
                'slug' => 'anggota-rating-nilai',
                'title' => 'Logika Perhitungan & Perankingan Pegawai',
                'explanation' => 'Kinerja pegawai dinilai dari rata-rata 4 formula utama: F1 (Penyelesaian Target), F2 (Kecepatan Pengiriman), F3 (Response Rate Kirim), dan F4 (Rating Kualitas). Hasil rata-rata base kemudian disesuaikan dengan koefisien fairness beban kerja.',
                'route_target' => 'dashboard',
                'roles_allowed' => ['Anggota Tim', 'Ketua Tim', 'Pimpinan', 'Admin'],
                'output' => 'Daftar peringkat pegawai di halaman peringkat kantor.',
                'form_details' => [
                    ['field' => 'Skor Performa Pegawai (F1–F4)', 'type' => 'Kalkulasi Sistem', 'required' => 'Tidak ada form', 'rules' => 'Rerata base F1–F4 disesuaikan dengan koefisien beban kerja (0.85 s.d 1.15) berdasarkan target pribadi dibandingkan rata-rata target kantor.', 'validation' => 'Hanya mengevaluasi penugasan aktif pada bulan evaluasi berjalan yang berstatus Diterima.']
                ],
                'tutorial' => '<ul class="list-disc pl-5 space-y-2">
                    <li><strong>F1 — Penyelesaian:</strong> Mengukur persentase volume target yang sudah selesai (cicilan dihargai proporsional secara kuadratik).</li>
                    <li><strong>F2 — Kecepatan:</strong> Mengukur kecepatan pengiriman berkas realisasi terhadap deadline penugasan.</li>
                    <li><strong>F3 — RR Kirim:</strong> Mengukur response rate keaktifan pelaporan tugas.</li>
                    <li><strong>F4 — Rating Kualitas:</strong> Rata-rata rating bintang dari Ketua Tim (1-5★) dikonversi ke skala 100%.</li>
                    <li><strong>Koefisien Beban Kerja:</strong> Memberikan bonus performa (hingga +15%) untuk pegawai dengan beban target besar, atau penyesuaian (hingga -15%) jika di bawah rata-rata.</li>
                </ul>',
                'sort_order' => 200,
            ],
            [
                'type' => 'user',
                'role_tab' => 'Anggota Tim',
                'menu_name' => '6. Batalkan Pengiriman & Aksi Lain',
                'slug' => 'anggota-batal-pengiriman',
                'title' => 'Membatalkan Laporan yang Telah Dikirim',
                'explanation' => 'Anggota Tim diperbolehkan untuk membatalkan atau menarik kembali pengiriman laporan pengerjaan tugas yang telah di-submit ke sistem **dengan syarat utama** laporan tersebut belum diperiksa (masih berstatus menunggu pemeriksaan) oleh Ketua Tim.',
                'route_target' => 'dashboard',
                'roles_allowed' => ['Anggota Tim'],
                'output' => 'Pengapusan data pengiriman realisasi dan pemulihan status penugasan.',
                'form_details' => [
                    ['field' => 'Aksi Batal Pengiriman (Button)', 'type' => 'Action Confirmation', 'required' => 'Ya', 'rules' => 'Hanya bisa dilakukan jika pengiriman berstatus Menunggu Pemeriksaan', 'validation' => 'Tombol batalkan akan dinonaktifkan/hilang jika laporan sudah dinilai Diterima atau Revisi oleh Ketua Tim.']
                ],
                'tutorial' => '<ol class="list-decimal pl-5 space-y-2">
                    <li>Buka halaman detail penugasan terkait di Tagihan Kerja.</li>
                    <li>Cari baris pengiriman laporan Anda yang berstatus "Menunggu Pemeriksaan".</li>
                    <li>Klik tombol **Batalkan Pengiriman** (ikon tempat sampah pada baris pengiriman).</li>
                </ol>',
                'sort_order' => 210,
            ],
        ];

        // ==================== DEVELOPER EXTENDED MAPPINGS ====================
        $devMeta = [
            'admin-kelola-bidang' => [
                'controller' => 'app/Http/Controllers/BidangController.php',
                'model' => 'app/Models/Bidang.php',
                'view' => 'resources/views/pages/main/admin/bidang/index.blade.php',
                'route' => 'Route::prefix(\'bidang-kerja\')->name(\'bidang.\')',
                'policy' => 'middleware(\'can:kelola-master-data\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_bidangs_table',
                'policy_path' => 'app/Providers/AuthServiceProvider.php',
                'tutorial' => '<p class="mb-3">Logika pembuatan bidang diimplementasikan di method <code>store</code>:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">public function store(Request $request) {
    $validated = $request->validate([
        \'nama_bidang\' => \'required|unique:bidangs,nama_bidang\',
        \'detail_bidang\' => \'required\',
        \'urutan\' => \'required|integer\',
    ]);
    Bidang::create($validated);
    return redirect()->back()->with(\'success\', \'Bidang berhasil ditambahkan\');
}</code></pre>'
            ],
            'admin-kelola-kegiatan' => [
                'controller' => 'app/Http/Controllers/JenisKegiatanController.php',
                'model' => 'app/Models/JenisKegiatan.php',
                'view' => 'resources/views/pages/main/admin/jenis-kegiatan/index.blade.php',
                'route' => 'Route::prefix(\'jenis-kegiatan\')->name(\'jenis-kegiatan.\')',
                'policy' => 'middleware(\'can:kelola-master-data\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_jenis_kegiatans_table',
                'policy_path' => 'app/Providers/AuthServiceProvider.php',
                'tutorial' => '<p class="mb-3">Menyimpan jenis kegiatan baru beserta penanda wajib DL/TL:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">public function store(Request $request) {
    $validated = $request->validate([
        \'jenis_kegiatan\' => \'required|unique:jenis_kegiatans\',
        \'kategori\' => \'required|in:Utama,Tambahan\',
        \'butuh_dl_atau_translok\' => \'required|boolean\',
    ]);
    JenisKegiatan::create($validated);
}</code></pre>'
            ],
            'admin-kelola-pegawai' => [
                'controller' => 'app/Http/Controllers/PegawaiRoleController.php',
                'model' => 'app/Models/Pegawai.php',
                'view' => 'resources/views/pages/main/admin/role-pegawai/index.blade.php',
                'route' => 'Route::prefix(\'role-pegawai\')->name(\'pegawai-role.\')',
                'policy' => '$this->authorize(\'kelola-master-data\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_pegawais_table',
                'policy_path' => 'app/Providers/AuthServiceProvider.php',
                'tutorial' => '<p class="mb-3">Sinkronisasi peran pegawai dilakukan dalam DB Transaction:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">DB::transaction(function () use ($validated, $roles) {
    $pegawai = Pegawai::create($validated);
    $pegawai->roles()->sync($roles);
});</code></pre>'
            ],
            'admin-kelola-pengumuman' => [
                'controller' => 'app/Http/Controllers/AnnouncementController.php',
                'model' => 'app/Models/Announcement.php',
                'view' => 'resources/views/pages/main/admin/announcements/index.blade.php',
                'route' => 'Route::prefix(\'announcements\')->name(\'announcements.\')',
                'policy' => 'middleware(\'can:kelola-pengumuman\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_announcements_table',
                'policy_path' => 'app/Providers/AuthServiceProvider.php',
                'tutorial' => '<p class="mb-3">Menyimpan pengumuman dengan upload file banner:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">if ($request->hasFile(\'image\')) {
    $path = $request->file(\'image\')->store(\'announcements\', \'public\');
    $validated[\'image\'] = $path;
}</code></pre>'
            ],
            'admin-kelola-sidebar-links' => [
                'controller' => 'app/Http/Controllers/SidebarLinkController.php',
                'model' => 'app/Models/SidebarLink.php',
                'view' => 'resources/views/pages/main/admin/sidebar-links/index.blade.php',
                'route' => 'Route::prefix(\'sidebar-links\')->name(\'sidebar-links.\')',
                'policy' => 'middleware(\'can:kelola-master-data\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_sidebar_links_table',
                'policy_path' => 'app/Providers/AuthServiceProvider.php',
                'tutorial' => '<p class="mb-3">Penggeseran urutan menu otomatis saat link baru disimpan dalam transaksi DB:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">DB::transaction(function () use ($validated, $isSpecial) {
    $parentId = ($validated[\'type\'] === \'sub\') ? $validated[\'parent_id\'] : null;
    SidebarLink::where(\'parent_id\', $parentId)
        ->where(\'sort_order\', \'>=\', $validated[\'sort_order\'])
        ->increment(\'sort_order\');
    SidebarLink::create($parentData);
});</code></pre>'
            ],
            'pimpinan-rk-iki' => [
                'controller' => 'app/Http/Controllers/RencanaJPTController.php',
                'model' => 'app/Models/RencanaJPT.php',
                'view' => 'resources/views/pages/main/admin/rk-iki-jpt/index.blade.php',
                'route' => 'Route::prefix(\'rencana-indikator-jpt\')->name(\'rencana-indikator-jpt.\')',
                'policy' => 'middleware(\'can:kelola-master-data\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_rencana_jpts_table',
                'policy_path' => 'app/Providers/AuthServiceProvider.php',
                'tutorial' => '<p class="mb-3">Pengambilan list Rencana JPT beserta relasi IKI JPT:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">$rencanaJpts = RencanaJPT::with([\'indikatorjpts\'])->get();</code></pre>'
            ],
            'pimpinan-agenda' => [
                'controller' => 'app/Http/Controllers/AgendaPimpinanController.php',
                'model' => 'app/Models/AgendaPimpinan.php',
                'view' => 'resources/views/pages/main/admin/agenda-pimpinan/index.blade.php',
                'route' => 'Route::prefix(\'agenda-pimpinan\')->name(\'agenda.\')',
                'policy' => 'middleware(\'can:kelola-master-data\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_agenda_pimpinans_table',
                'policy_path' => 'app/Providers/AuthServiceProvider.php',
                'tutorial' => '<p class="mb-3">Metode konversi agenda pimpinan ke baris CKP utama:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">// Logika konversi agenda ke CKP
$ckp = CkpPegawai::create([
    \'uraian\' => $agenda->nama_agenda,
    \'target\' => $agenda->target,
    \'realisasi\' => $agenda->realisasi,
]);</code></pre>'
            ],
            'pimpinan-acc-dl' => [
                'controller' => 'app/Http/Controllers/MasterKegiatanController.php',
                'model' => 'app/Models/Penugasan.php',
                'view' => 'resources/views/pages/main/pegawai/rencana-kerja/rencana-kerja-dl.blade.php',
                'route' => 'Route::get(\'/rencana-kerja-dl\')',
                'policy' => 'middleware(\'can:acceptDL,App\Models\Penugasan,penugasan\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_penugasans_table',
                'policy_path' => 'app/Policies/PenugasanPolicy.php',
                'tutorial' => '<p class="mb-3">Pimpinan menyetujui status DL yang diajukan Ketua Tim:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">public function update_rk_dl(Request $request, Penugasan $penugasan) {
    $penugasan->update([\'status_dl\' => $request->status]);
}</code></pre>'
            ],
            'pimpinan-push-reminder' => [
                'controller' => 'app/Http/Controllers/DashboardController.php',
                'model' => 'app/Models/Pegawai.php',
                'view' => 'resources/views/pages/dashboard.blade.php',
                'route' => 'Route::post(\'/pegawai/{pegawai}/send-todo-reminder\')',
                'policy' => 'auth',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_pegawais_table',
                'tutorial' => '<p class="mb-3">Mengirim notifikasi push manual ke gawai pegawai:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">public function sendPegawaiTodoReminder(Pegawai $pegawai) {
    app(PushNotificationService::class)->sendPush($pegawai, \'Pengingat Tugas\', \'Anda memiliki tugas belum selesai.\');
}</code></pre>'
            ],
            'ketua-buat-kegiatan-baru' => [
                'controller' => 'app/Http/Controllers/KegiatanController.php',
                'model' => 'app/Models/Kegiatan.php',
                'view' => 'resources/views/pages/main/pegawai/rencana-kerja/index.blade.php',
                'route' => 'Route::post(\'/kegiatan/bidang/{bidang}\')',
                'policy' => 'middleware(\'can:create,App\Models\Kegiatan\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_kegiatans_table',
                'policy_path' => 'app/Policies/KegiatanPolicy.php',
                'tutorial' => '<p class="mb-3">Membuat Kegiatan baru di bawah naungan Bidang Kerja:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">Kegiatan::create([
    \'id_bidang\' => $request->id_bidang,
    \'nama_rk_kegiatan\' => $request->nama_rk_kegiatan,
    \'id_penanggung_jawab\' => auth()->id(),
]);</code></pre>'
            ],
            'ketua-buat-sub-kegiatan' => [
                'controller' => 'app/Http/Controllers/SubKegiatanController.php',
                'model' => 'app/Models/SubKegiatan.php',
                'view' => 'resources/views/pages/main/pegawai/rencana-kerja/detail-sub-kegiatan.blade.php',
                'route' => 'Route::post(\'/kegiatan/{kegiatan}/sub-kegiatan\')',
                'policy' => 'middleware(\'can:create,kegiatan\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_sub_kegiatans_table',
                'policy_path' => 'app/Policies/KegiatanPolicy.php',
                'tutorial' => '<p class="mb-3">Membuat Sub-Kegiatan baru di bawah Kegiatan utama:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">$sub = $kegiatan->subKegiatans()->create([
    \'nama_sub_kegiatan\' => $request->nama_sub_kegiatan,
    \'target\' => $request->target,
]);</code></pre>'
            ],
            'ketua-buat-penugasan' => [
                'controller' => 'app/Http/Controllers/PenugasanController.php',
                'model' => 'app/Models/Penugasan.php',
                'view' => 'resources/views/pages/main/pegawai/rencana-kerja/detail-sub-kegiatan.blade.php',
                'route' => 'Route::post(\'/sub-kegiatan/{subKegiatan}/penugasan\')',
                'policy' => 'middleware(\'can:create,App\Models\Penugasan,subKegiatan\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_penugasans_table',
                'policy_path' => 'app/Policies/PenugasanPolicy.php',
                'tutorial' => '<p class="mb-3">Mengecek overlap tanggal DL/TL sebelum menugaskan anggota:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">$bentrok = Penugasan::where(\'id_anggota\', $id)->where(function($q) use ($start, $end) {
    $q->whereBetween(\'tanggal_mulai\', [$start, $end])
      ->orWhereBetween(\'tanggal_selesai\', [$start, $end]);
})->exists();</code></pre>'
            ],
            'ketua-terima-pengiriman' => [
                'controller' => 'app/Http/Controllers/PenerimaanController.php',
                'model' => 'app/Models/Penerimaan.php',
                'view' => 'resources/views/pages/main/pegawai/tagihan-kerja/index.blade.php',
                'route' => 'Route::post(\'/penugasan/{penugasan}/pengirimans/{pengirimans}/penerimaan\')',
                'policy' => 'middleware(\'can:receive,penugasan\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_penerimaans_table',
                'policy_path' => 'app/Policies/PenugasanPolicy.php',
                'tutorial' => '<p class="mb-3">Ketua Tim menerima berkas pengiriman anggota:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">Penerimaan::create([
    \'id_pengiriman\' => $pengiriman->id_pengiriman,
    \'status\' => \'Diterima\',
    \'jumlah_diterima\' => $request->jumlah_diterima,
]);</code></pre>'
            ],
            'ketua-konversi-ckp' => [
                'controller' => 'app/Http/Controllers/CkpPegawaiController.php',
                'model' => 'app/Models/CkpPegawai.php',
                'view' => 'resources/views/pages/main/pegawai/tagihan-kerja/ckp-pegawai.blade.php',
                'route' => 'Route::prefix(\'ckp\')->group(...)',
                'policy' => 'auth',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'recreate_ckp_pegawais_table',
                'tutorial' => '<p class="mb-3">Konversi sub-kegiatan berprogres menjadi CKP Ketua Tim:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">$ckp = CkpPegawai::create([
    \'uraian\' => $sub->nama_sub_kegiatan,
    \'target\' => 100,
    \'realisasi\' => $sub->progres_persen,
]);</code></pre>'
            ],
            'ketua-transfer-kegiatan-tim' => [
                'controller' => 'app/Http/Controllers/KegiatanTransferController.php',
                'model' => 'app/Models/Kegiatan.php',
                'view' => 'resources/views/pages/main/pegawai/rencana-kerja/index.blade.php',
                'route' => 'Route::post(\'/kegiatan/{kegiatan}/transfer\')',
                'policy' => 'middleware(\'can:transfer,kegiatan\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_kegiatan_transfers_table',
                'policy_path' => 'app/Policies/KegiatanPolicy.php',
                'tutorial' => '<p class="mb-3">Mentransfer kegiatan dan memperbarui flag transfer:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">$kegiatan->update([
    \'id_penanggung_jawab\' => $request->to_ketua_id,
    \'transferred_at\' => now(),
]);</code></pre>'
            ],
            'ketua-edit-hapus' => [
                'controller' => 'app/Http/Controllers/KegiatanController.php',
                'model' => 'app/Models/Kegiatan.php',
                'view' => 'resources/views/pages/main/pegawai/rencana-kerja/index.blade.php',
                'route' => 'Route::delete(\'/kegiatan/{kegiatan}\')',
                'policy' => 'middleware(\'can:delete,kegiatan\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_kegiatans_table',
                'policy_path' => 'app/Policies/KegiatanPolicy.php',
                'tutorial' => '<p class="mb-3">Sebelum menghapus kegiatan, pastikan tidak ada transaksi berjalan:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">if ($kegiatan->subKegiatans()->whereHas(\'penugasans.pengirimans\')->exists()) {
    throw new \Exception(\'Kegiatan tidak dapat dihapus karena sudah ada realisasi!\');
}</code></pre>'
            ],
            'ketua-rating-nilai' => [
                'controller' => 'app/Services/DashboardAnalyticsService.php',
                'model' => 'app/Models/Penugasan.php',
                'view' => 'resources/views/pages/dashboard.blade.php',
                'route' => 'Route::get(\'/\')',
                'policy' => 'auth',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_penugasans_table',
                'tutorial' => '<p class="mb-3">Perhitungan nilai kinerja Ketua Tim (F5 + Fairness Beban Kerja Tim):</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">// Skor dasar murni F5 (Pengawasan Tim)
$baseScore = $f5;
$koef = $avgTargetTim > 0 ? ($item->total_target_tim / $avgTargetTim) : 1.0;
$koef = min(1.15, max(0.85, $koef));
$finalScore = $baseScore * $koef; // dengan pembatasan bonus ruang ke 100%</code></pre>'
            ],
            'anggota-pantau-todo' => [
                'controller' => 'app/Http/Controllers/DashboardController.php',
                'model' => 'app/Models/Penugasan.php',
                'view' => 'resources/views/pages/dashboard.blade.php',
                'route' => 'Route::get(\'/\')',
                'policy' => 'auth',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_penugasans_table',
                'tutorial' => '<p class="mb-3">Query mengambil tugas berjalan milik anggota yang sedang login:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">$penugasans = Penugasan::where(\'id_anggota\', auth()->id())->where(\'status\', \'Belum Dikirim\')->get();</code></pre>'
            ],
            'anggota-kirim-laporan' => [
                'controller' => 'app/Http/Controllers/PengirimanController.php',
                'model' => 'app/Models/Pengiriman.php',
                'view' => 'resources/views/pages/main/pegawai/tagihan-kerja/index.blade.php',
                'route' => 'Route::post(\'/penugasan/{penugasan}/pengirimans\')',
                'policy' => 'middleware(\'can:send,penugasan\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_pengirimans_table',
                'policy_path' => 'app/Policies/PenugasanPolicy.php',
                'tutorial' => '<p class="mb-3">Menyimpan pengiriman realisasi tugas baru:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">Pengiriman::create([
    \'id_penugasan\' => $penugasan->id_penugasan,
    \'jumlah_dikirim\' => $request->jumlah_dikirim,
    \'bukti_dukung\' => $request->bukti_dukung,
]);</code></pre>'
            ],
            'anggota-revisi-laporan' => [
                'controller' => 'app/Http/Controllers/PengirimanController.php',
                'model' => 'app/Models/Pengiriman.php',
                'view' => 'resources/views/pages/main/pegawai/tagihan-kerja/index.blade.php',
                'route' => 'Route::post(\'/penugasan/{penugasan}/pengirimans\')',
                'policy' => 'middleware(\'can:send,penugasan\')',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_pengirimans_table',
                'policy_path' => 'app/Policies/PenugasanPolicy.php',
                'tutorial' => '<p class="mb-3">Kirim ulang laporan revisi (sama dengan store tetapi status sebelumnya dinonaktifkan):</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">// Logic kirim ulang revisi
$pengiriman->update([\'status\' => \'Revisi\']);</code></pre>'
            ],
            'anggota-konversi-ckp-tim' => [
                'controller' => 'app/Http/Controllers/CkpPegawaiController.php',
                'model' => 'app/Models/CkpPegawai.php',
                'view' => 'resources/views/pages/main/pegawai/tagihan-kerja/ckp-pegawai.blade.php',
                'route' => 'Route::prefix(\'ckp\')->group(...)',
                'policy' => 'auth',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'recreate_ckp_pegawais_table',
                'tutorial' => '<p class="mb-3">Mengambil penugasan berstatus diterima untuk bulan target:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">$accepted = Penugasan::where(\'id_anggota\', auth()->id())->whereHas(\'pengirimans.penerimaan\', function($q) {
    $q->where(\'status\', \'Diterima\');
})->get();</code></pre>'
            ],
            'anggota-rating-nilai' => [
                'controller' => 'app/Services/KinerjaCalculatorService.php',
                'model' => 'app/Models/Penugasan.php',
                'view' => 'resources/views/pages/dashboard.blade.php',
                'route' => 'Route::get(\'/\')',
                'policy' => 'auth',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_penugasans_table',
                'tutorial' => '<p class="mb-3">Perhitungan nilai kinerja dihitung secara global:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">$skor = ($rrKirim + $rrTerima + $waktu + $kualitas) / 4 * $koefisienBeban;</code></pre>'
            ],
            'anggota-batal-pengiriman' => [
                'controller' => 'app/Http/Controllers/PengirimanController.php',
                'model' => 'app/Models/Pengiriman.php',
                'view' => 'resources/views/pages/main/pegawai/tagihan-kerja/index.blade.php',
                'route' => 'Route::delete(\'pengirimans/{pengiriman}\')',
                'policy' => 'auth',
                'middleware' => 'auth, active.pegawai',
                'migration_pattern' => 'create_pengirimans_table',
                'tutorial' => '<p class="mb-3">Membatalkan pengiriman berkas jika belum diperiksa:</p><pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code class="language-php">if ($pengiriman->status === \'Menunggu Pemeriksaan\') {
    $pengiriman->delete();
}</code></pre>'
            ],
        ];

        // Seed user-facing guides first
        foreach ($data as $item) {
            PanduanFitur::create($item);
        }

        // Programmatically generate dev-facing guides mirroring the menu structure
        foreach ($data as $item) {
            $slug = $item['slug'];
            if (isset($devMeta[$slug])) {
                $meta = $devMeta[$slug];
                
                // Read Migration Code
                $migrationPath = null;
                $migrationCode = null;
                if (!empty($meta['migration_pattern'])) {
                    $migrationPattern = base_path('database/migrations/*' . $meta['migration_pattern'] . '*.php');
                    $migrationFiles = glob($migrationPattern);
                    if (!empty($migrationFiles)) {
                        $firstFile = $migrationFiles[0];
                        $migrationPath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $firstFile);
                        $migrationPath = str_replace('\\', '/', $migrationPath);
                        $migrationCode = file_get_contents($firstFile);
                    }
                }
                
                // Read Model Code
                $modelCode = null;
                if (!empty($meta['model']) && file_exists(base_path($meta['model']))) {
                    $modelCode = file_get_contents(base_path($meta['model']));
                }
                
                // Read Controller Code
                $controllerCode = null;
                if (!empty($meta['controller']) && file_exists(base_path($meta['controller']))) {
                    $controllerCode = file_get_contents(base_path($meta['controller']));
                }

                // Read Policy Code
                $policyCode = null;
                if (!empty($meta['policy_path']) && file_exists(base_path($meta['policy_path']))) {
                    $policyCode = file_get_contents(base_path($meta['policy_path']));
                }

                PanduanFitur::create([
                    'type' => 'developer',
                    'role_tab' => $item['role_tab'],
                    'menu_name' => $item['menu_name'],
                    'slug' => 'dev-' . $slug,
                    'title' => '[DEV] ' . $item['title'],
                    'explanation' => '<strong>Lokasi Teknis:</strong> Fitur ini melayani ' . strtolower($item['title']) . '. Berikut rincian arsitektur berkas, route, policy, middleware, dan referensi implementasi kode.',
                    'route_target' => $item['route_target'],
                    'tutorial' => $meta['tutorial'],
                    'roles_allowed' => ['Developer'],
                    'output' => $item['output'],
                    'form_details' => $item['form_details'],
                    'controller_path' => $meta['controller'],
                    'model_path' => $meta['model'],
                    'view_path' => $meta['view'],
                    'route_definition' => $meta['route'],
                    'policy_gate' => $meta['policy'],
                    'middleware' => $meta['middleware'],
                    'migration_path' => $migrationPath,
                    'migration_code' => $migrationCode,
                    'model_code' => $modelCode,
                    'controller_code' => $controllerCode,
                    'policy_path' => $meta['policy_path'] ?? null,
                    'policy_code' => $policyCode,
                    'sort_order' => $item['sort_order'],
                ]);
            }
        }
    }
}
