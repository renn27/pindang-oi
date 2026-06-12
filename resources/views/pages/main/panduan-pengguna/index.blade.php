@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="Panduan Pengguna" />

    @php
        $roleGuides = [
            [
                'role' => 'Admin',
                'summary' => 'Menyiapkan struktur master data operasional dan mengelola akses/status keaktifan pegawai.',
                'menus' => ['Dashboard', 'Admin', 'Rencana Kinerja', 'Tagihan Kerja', 'Kalender', 'Pengumuman'],
                'tasks' => [
                    '<strong>Kelola Bidang:</strong> Mengatur daftar Bidang Kerja untuk menentukan daftar bidang yang muncul di Tagihan Kerja.',
                    '<strong>Kelola Kegiatan:</strong> Mengatur Jenis Kegiatan (termasuk referensi wajib DL atau Translok).',
                    '<strong>Kelola Pegawai:</strong> Mengatur Data & Role Pegawai (Admin, Pimpinan, Ketua Tim, Anggota Tim).',
                    '<strong>Deaktivasi Pegawai:</strong> Menonaktifkan pegawai dengan pengisian bulan mulai nonaktif (inactive_from_month).',
                    '<strong>Kelola Pengumuman:</strong> Membuat Pengumuman penting agar tampil di dasbor seluruh pegawai.',
                    '<strong>Setup Multi-Role:</strong> Mengonfigurasi satu pegawai agar memegang beberapa peran sekaligus jika dibutuhkan fungsi kerja.',
                    '<strong>Asisten AI (Smart Draft):</strong> Memanfaatkan asisten AI untuk menyusun draft kegiatan master secara cepat.',
                ],
                'steps' => [
                    '<strong>Pilih Master Data:</strong> Buka menu Admin lalu pilih master data yang ingin dikelola (Bidang, Jenis Kegiatan, Pegawai, Pengumuman).',
                    '<strong>Entri Pegawai:</strong> Pada manajemen pegawai, tambahkan data pegawai baru, atur role struktural, dan tentukan role aktif pertamanya.',
                    '<strong>Atur Nonaktif:</strong> Gunakan tombol nonaktifkan pegawai untuk pegawai yang mutasi/pensiun dengan memilih bulan mulai nonaktif.',
                    '<strong>Transfer Ketua Tim:</strong> Jika menonaktifkan Ketua Tim, pastikan semua kegiatan aktif yang dipimpin sudah ditransfer terlebih dahulu.',
                    '<strong>Monitoring Sistem:</strong> Pantau statistik umum dashboard sistem tanpa membuat CKP pribadi.',
                    '<strong>Gunakan Asisten AI:</strong> Klik tombol Asisten AI di halaman Tagihan Kerja untuk membuat rancangan master kegiatan baru secara otomatis.',
                ],
                'examples' => [
                    '<strong>Pegawai Nonaktif:</strong> Menonaktifkan Andi per Juli 2026 berarti Andi dihapus dari ranking dan todo list terhitung mulai bulan Juli.',
                    '<strong>Role Otomatis:</strong> Memberikan role Ketua Tim ke Bunga instan memunculkan menu pengelolaan Rencana Kerja Per Fungsi.',
                    '<strong>Pengumuman Sistem:</strong> Pengumuman "Batas akhir CKP Juni adalah 30 Juni" akan langsung muncul di dasbor semua akun.',
                    '<strong>AI Draft Kegiatan:</strong> Mengetik "buat kegiatan baru Survei Hortikultura tahun 2026 dengan RK JPT Dukungan Manajemen" di Asisten AI.',
                ],
                'notes' => [
                    '<strong>Tanpa CKP Mandiri:</strong> Admin bertindak sebagai super user dan tidak memiliki pelaporan CKP pribadi.',
                    '<strong>Proteksi Akun Sendiri:</strong> Sistem memblokir upaya admin menonaktifkan akun yang sedang aktif digunakan saat itu.',
                    '<strong>Dampak Master Data:</strong> Perubahan bidang/jenis kegiatan akan langsung memengaruhi pilihan formulir rencana kerja.',
                ],
            ],
            [
                'role' => 'Pimpinan',
                'summary' => 'Mengendalikan RK/IKI pimpinan, memberikan persetujuan perjalanan dinas (DL/TL), memantau CKP, dan mengirim pengingat manual.',
                'menus' => ['Dashboard', 'Pimpinan', 'Rencana Kinerja', 'Tagihan Kerja', 'Kalender', 'CKP Saya', 'Pengumuman'],
                'tasks' => [
                    '<strong>Penyusunan RK/IKI:</strong> Menyusun Rencana Kinerja (RK) &amp; Indikator Kinerja Individu (IKI) Pimpinan.',
                    '<strong>Kelola Agenda:</strong> Mengelola Agenda Pimpinan dan menjadikannya CKP Pimpinan bulanan.',
                    '<strong>Persetujuan Perjalanan:</strong> Menyetujui (ACC) atau menolak pengajuan Dinas Luar / Translok dari Ketua Tim.',
                    '<strong>Batal Kalender DL:</strong> Mencabut/menghapus data Kalender DL/Translok jika dibatalkan (sebelum jadi CKP).',
                    '<strong>Kirim Push Reminder:</strong> Mengirim pengingat todo list (Push Notification) manual kepada pegawai langsung dari dasbor.',
                    '<strong>Ekspor Rekap MPH Global:</strong> Mengunduh kompilasi Matriks Pemantauan Harian (MPH) seluruh fungsi kantor ke Excel.',
                ],
                'steps' => [
                    '<strong>Entry RK/IKI:</strong> Buka menu Pimpinan untuk mengisi RK &amp; IKI JPT Pimpinan sebagai dasar cascading target.',
                    '<strong>Tinjau Ajuan DL:</strong> Buka menu Rencana Kinerja -> Rencana Kerja Perlu DL/Translok untuk meninjau ajuan perjalanan.',
                    '<strong>Eksekusi Persetujuan:</strong> Klik "ACC" untuk menyetujui perjalanan (otomatis masuk Kalender DL) atau "Tolak" dengan memberi catatan revisi.',
                    '<strong>Monitoring &amp; Reminder:</strong> Buka Dashboard, lihat tabel Rekap Penugasan Pegawai, klik "Kirim Pengingat" jika ada tugas menumpuk/terlewat.',
                    '<strong>Audit Ekspor Excel:</strong> Lihat Laporan CKP Pegawai untuk mengaudit log unduhan excel CKP masing-masing pegawai.',
                    '<strong>Unduh Laporan MPH:</strong> Akses menu Rencana Kinerja, lalu gunakan tombol ekspor MPH All untuk meninjau total progres kantor.',
                ],
                'examples' => [
                    '<strong>ACC Perjalanan:</strong> Menyetujui perjalanan Andi tanggal 12-14 Juni: Andi otomatis dilabeli "DL" pada kalender DL/Translok.',
                    '<strong>Push Notification:</strong> Mengirim pengingat manual ke Bunga yang memiliki 3 tugas terlewat: Bunga menerima push notification instan di devicenya.',
                    '<strong>Konversi Agenda:</strong> Menjadikan agenda rapat koordinasi pimpinan dengan realisasi 1/1 menjadi baris CKP Utama.',
                ],
                'notes' => [
                    '<strong>Penguncian Kalender:</strong> Persetujuan DL/TL yang sudah dijadikan CKP oleh anggota tim terkunci dan tidak bisa dibatalkan dari kalender.',
                    '<strong>Proporsi Kualitas:</strong> Penilaian kualitas CKP Pimpinan dihitung proporsional dari rasio realisasi agenda terhadap target (dikali 20).',
                ],
            ],
            [
                'role' => 'Ketua Tim',
                'summary' => 'Menyusun kegiatan per fungsi, mendelegasikan tugas ke anggota, menilai pengiriman, dan membuat CKP Ketua Tim.',
                'menus' => ['Dashboard', 'Rencana Kinerja', 'Tagihan Kerja', 'Kalender', 'CKP Saya', 'Pengumuman'],
                'tasks' => [
                    '<strong>Susun Kegiatan Fungsi:</strong> Membuat Rencana Kerja Per Fungsi (kegiatan, sub kegiatan, target, satuan, periode).',
                    '<strong>Delegasi &amp; Setup DL:</strong> Membagi sub kegiatan menjadi penugasan anggota dengan menandai kebutuhan DL/Translok.',
                    '<strong>Evaluasi Laporan:</strong> Memeriksa pengiriman anggota (Terima dengan rating bintang 1-5 atau kembalikan untuk Revisi).',
                    '<strong>Transfer Tanggung Jawab:</strong> Mengalihkan (Transfer) kepemilikan Kegiatan ke Ketua Tim lain jika diperlukan.',
                    '<strong>Penyusunan CKP Tim:</strong> Membuat CKP Ketua Tim dari sub kegiatan yang progresnya sudah berjalan.',
                    '<strong>Ekspor Matriks MPH:</strong> Mengekspor Matriks Pemantauan Harian (MPH) per bidang kerja ke file Excel.',
                    '<strong>Switch Peran Aktif:</strong> Berpindah peran aktif secara cepat jika memegang multi-role struktural.',
                    '<strong>Asisten AI (Smart Draft):</strong> Memanfaatkan asisten AI untuk mendraf kegiatan, sub kegiatan, dan penugasan secara massal dengan bahasa sehari-hari.',
                ],
                'steps' => [
                    '<strong>Buat Rencana Kerja:</strong> Buka Rencana Kinerja -> Rencana Kerja Per Fungsi, pilih bidang, lalu tambahkan Kegiatan baru.',
                    '<strong>Setup Sub-Kegiatan:</strong> Tambahkan daftar Sub Kegiatan beserta Anggota, Jenis Kegiatan, Target, dan rentang tanggal mulai/selesai.',
                    '<strong>Verifikasi Laporan:</strong> Pantau Tagihan Kerja. Jika anggota mengirim hasil, periksa bukti dukung lalu klik Terima/Revisi.',
                    '<strong>Revisi Ajuan Dinas:</strong> Jika pengajuan DL/TL ditolak Pimpinan, perbaiki tanggal/kegiatan melalui Todo List Ketua Tim -> Revisi DL.',
                    '<strong>Konversi ke CKP:</strong> Gunakan tombol "Buat CKP" pada sub kegiatan di bulan yang sesuai untuk mendaftarkannya ke CKP Saya.',
                    '<strong>Unduh Laporan MPH:</strong> Masuk ke menu Rencana Kerja, klik bidang Anda, lalu tekan tombol <strong>Export MPH</strong>.',
                    '<strong>Pindah Tampilan Peran:</strong> Jika memiliki multi-role, klik foto profil di kanan atas, lalu pilih peran aktif yang diinginkan.',
                    '<strong>Gunakan Asisten AI:</strong> Klik tombol <strong>Asisten AI</strong> di kanan atas halaman Tagihan Kerja, ketik instruksi Anda (misal: "tugaskan Andi survei target 10"), lalu terapkan ke formulir.',
                ],
                'examples' => [
                    '<strong>Penilaian Tuntas:</strong> Sub kegiatan "Validasi Dokumen", Andi dikasih target 30. Andi kirim 30 (Pelunasan), Ketua Tim memberi rating 5 bintang.',
                    '<strong>Mutasi Kegiatan:</strong> Melakukan transfer kegiatan "Survei Pertanian" kepada Ketua Tim B karena adanya perubahan alokasi fungsi kerja.',
                    '<strong>Hitung CKP Juni:</strong> Membuat CKP Ketua Tim bulan Juni dari sub kegiatan yang target total penugasannya 100 dan realisasi diterima 100.',
                    '<strong>AI Multi-Drafting:</strong> Menginstruksikan AI "tugaskan Andi, Budi, dan Cici mengolah data target 15 dokumen dari tanggal 15-20 Juni", lalu menyalin info yang sama ke ketiganya via chat.',
                ],
                'notes' => [
                    '<strong>Batas Bidang Kerja:</strong> Ketua Tim hanya bisa mengelola kegiatan/sub kegiatan yang berada di bawah kewenangan bidangnya.',
                    '<strong>Pembagian CKP Transfer:</strong> Hak pembuatan CKP sub-kegiatan 100% selesai sebelum tanggal transfer berada pada Ketua Tim lama, sisanya beralih ke Ketua Tim baru.',
                ],
            ],
            [
                'role' => 'Anggota Tim',
                'summary' => 'Melihat tugas berjalan/terlewat/revisi, mengirimkan hasil pekerjaan (cicilan/pelunasan), dan menyusun CKP bulanan.',
                'menus' => ['Dashboard', 'Tagihan Kerja', 'Kalender', 'CKP Saya', 'Pengumuman'],
                'tasks' => [
                    '<strong>Pantau Prioritas:</strong> Memantau prioritas pekerjaan dari dasbor pribadi (Todo List Anggota).',
                    '<strong>Kirim Hasil Kerja:</strong> Mengirim progres hasil pekerjaan (Cicilan) atau penyelesaian akhir (Pelunasan).',
                    '<strong>Revisi Pengiriman:</strong> Memperbaiki hasil kerja yang dikembalikan (status Revisi) berdasarkan catatan Ketua Tim.',
                    '<strong>Jadikan CKP:</strong> Menjadikan penugasan yang sudah diterima sebagai baris CKP Saya.',
                    '<strong>Ekspor Laporan:</strong> Mengedit detail CKP di CKP Saya dan mengunduh Excel CKP-R per bulan.',
                    '<strong>Kelola Preferensi Push:</strong> Mengatur izin dan frekuensi pengiriman Push Notification Todo bulanan di halaman profil.',
                    '<strong>Switch Peran Aktif:</strong> Berpindah peran aktif secara cepat jika memegang multi-role struktural.',
                ],
                'steps' => [
                    '<strong>Cek Dasbor Pribadi:</strong> Masuk ke Dashboard untuk melihat rincian tugas Berjalan, Terlewat, dan Revisi Ketua Tim.',
                    '<strong>Pilih Tugas Aktif:</strong> Buka Tagihan Kerja, buka sub-kegiatan terkait, klik "Buat Pengiriman" pada baris nama Anda.',
                    '<strong>Submit Laporan:</strong> Pilih tipe pengiriman (Cicilan/Pelunasan), isi jumlah dikirim, bulan pengiriman (YYYY-MM), bukti dukung, dan kirim.',
                    '<strong>Kirim Ulang Revisi:</strong> Jika direvisi, baca catatan Ketua Tim di histori pengiriman, perbaiki bukti, lalu kirim ulang.',
                    '<strong>Proses Ekspor Akhir:</strong> Jika diterima, klik "Jadikan CKP" pada bulan yang sesuai. Buka CKP Saya, periksa data, lalu klik "Export Excel".',
                    '<strong>Aktifkan Pengingat Mandiri:</strong> Buka halaman Profil Saya -> Preferensi Notifikasi, aktifkan web push, dan atur reminder.',
                    '<strong>Pindah Tampilan Peran:</strong> Jika memiliki multi-role, klik foto profil di kanan atas, lalu pilih peran aktif yang diinginkan.',
                ],
                'examples' => [
                    '<strong>Pengisian Akumulatif:</strong> Target 50 dokumen. Kirim cicilan pertama 20 pada bulan Juni. Kirim pelunasan kedua 50 (akumulatif total), bukan sisa 30.',
                    '<strong>Penyesuaian Redaksi:</strong> Menjadikan tugas "Pengolahan Data Survei" yang diterima menjadi CKP Juni dengan Uraian yang disesuaikan format formal.',
                ],
                'notes' => [
                    '<strong>Lock Status Dinas:</strong> Tombol "Buat Pengiriman" terkunci (disabled) jika penugasan butuh DL/Translok dan belum di-ACC oleh Pimpinan.',
                    '<strong>Batas Ekspor Excel:</strong> Ekspor Excel CKP hanya diperbolehkan per bulan (tidak bisa "Semua Bulan") dan dibatasi cooldown 1 menit per unduhan untuk keamanan server.',
                    '<strong>Realisasi Pelunasan:</strong> Pastikan jumlah pengiriman pelunasan diisi secara akumulatif (total akhir) agar perhitungan RR Kirim mencapai 100%.',
                ],
            ],
        ];

        $importantTerms = [
            ['term' => 'CKP-R', 'definition' => 'Capaian Kinerja Pegawai - Realisasi. Format dokumen ekspor Excel bulanan untuk laporan kinerja resmi.'],
            ['term' => 'Pelunasan', 'definition' => 'Tipe pengiriman akhir penugasan yang menyatakan pekerjaan telah rampung sepenuhnya (diisi nilai total target).'],
            ['term' => 'Cicilan', 'definition' => 'Tipe pengiriman bertahap yang menunjukkan progres sebagian pekerjaan pada bulan berjalan.'],
            ['term' => 'Koefisien Beban', 'definition' => 'Faktor pengali ranking berdasarkan beban target tugas yang dipikul pegawai dibandingkan dengan beban kerja rata-rata pegawai lain.'],
            ['term' => 'Push Reminder', 'definition' => 'Notifikasi web instan yang dikirim pimpinan ke browser/HP pegawai untuk mengingatkan tugas-tugas yang terlewat.'],
            ['term' => 'Transferred At', 'definition' => 'Tanggal pencatatan pemindahan kegiatan antar Ketua Tim yang menentukan batas pembagian hak pengisian CKP sub kegiatan.'],
            ['term' => 'Asisten AI', 'definition' => 'Fitur asisten pintar berbasis LLM (Gemini) di halaman Tagihan Kerja untuk membantu menyusun draf Kegiatan, Sub Kegiatan, dan Penugasan dari teks instruksi bebas.'],
        ];

        $calculationCards = [
            [
                'title' => 'RR Kirim & RR Terima (Rasio Realisasi)',
                'formula' => 'RR = min((jumlah_realisasi / target) x 100, 100)',
                'description' => 'Mengukur ketepatan volume kerja yang dikirim anggota atau diterima ketua tim terhadap target awal.',
                'example' => 'Target 40, realisasi dikirim 30. RR = 30 / 40 x 100 = 75%. Jika realisasi diisi 50 (melebihi target), nilai RR tetap dibatasi maksimal 100%.',
            ],
            [
                'title' => 'Rating Ketepatan Waktu',
                'formula' => '0 hari telat = 5★ | 1 hari = 4★ | 2 hari = 3★ | 3 hari = 2★ | > 3 hari = 1★',
                'description' => 'Sistem membandingkan tanggal kirim/terima riil terhadap tanggal deadline efektif yang ditentukan pada penugasan.',
                'example' => 'Tenggat waktu 15 Juni. Anggota mengirim berkas pada tanggal 17 Juni (terlambat 2 hari). Maka rating waktu kirim otomatis mendapat nilai 3 bintang.',
            ],
            [
                'title' => 'Tingkat Kualitas CKP',
                'formula' => 'Tingkat Kualitas (%) = Rating Bintang x 20',
                'description' => 'Mengonversi skor penilaian subjektif ketua tim (skala 1-5 bintang) ke dalam format persentase penilaian CKP formal BPS.',
                'example' => 'Ketua Tim memberikan rating 4 bintang atas kualitas berkas yang diunggah anggota. Tingkat kualitas CKP = 4 x 20 = 80%.',
            ],
            [
                'title' => 'Skor Perankingan Kinerja Akhir',
                'formula' => 'Skor = ((F1 + F2 + F3 + F4) / 4) x Koefisien Beban Kerja',
                'description' => 'F1 = Penyelesaian, F2 = Kecepatan, F3 = RR Kirim, F4 = Rating Persen. Koefisien Beban Kerja membandingkan total target individu dengan rata-rata target kantor (dibatasi 0.85 - 1.15).',
                'example' => 'Rata-rata faktor = 90%. Beban kerja pegawai tinggi sehingga Koefisien = 1.05. Skor Akhir = 90% x 1.05 = 94.50%.',
            ],
        ];
    @endphp

    <div class="space-y-6">
        {{-- ===== HEADER PENGANTAR ===== --}}
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="grid gap-6 lg:grid-cols-[2fr_1fr] lg:items-center">
                <div>
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <div class="mb-1.5">
                                <span class="inline-flex items-center gap-2 rounded-lg border border-brand-100 bg-brand-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-brand-700 dark:border-brand-500/20 dark:bg-brand-500/10 dark:text-brand-300">
                                    Sistem Monitoring Kinerja
                                </span>
                            </div>
                            <h1 class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-white md:text-3xl">
                                Pusat Panduan &amp; Dokumentasi Sistem
                            </h1>
                        </div>
                        <div class="shrink-0 flex items-center">
                            <img class="dark:hidden h-11 w-auto" src="/images/logo/logo.svg" alt="Pindang OI Logo" />
                            <img class="hidden dark:block h-11 w-auto" src="/images/logo/logo-dark.svg" alt="Pindang OI Logo" />
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                        Selamat datang di halaman bantuan Pindang OI. Halaman ini memuat alur kerja operasional, ketentuan validasi backend (hidden rules), logika perhitungan penilaian kinerja, serta glosarium untuk mempermudah pemahaman dan penggunaan sistem sehari-hari.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1 rounded-lg border border-brand-100 bg-brand-50/50 px-2.5 py-1 text-xs font-semibold text-brand-700 dark:border-brand-500/20 dark:bg-brand-500/10 dark:text-brand-300">
                            <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Validasi Dinas Luar
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-100 bg-emerald-50/50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Cascading CKP
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-lg border border-purple-100 bg-purple-50/50 px-2.5 py-1 text-xs font-semibold text-purple-700 dark:border-purple-500/20 dark:bg-purple-500/10 dark:text-purple-300">
                            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Perankingan Kinerja
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-lg border border-indigo-100 bg-indigo-50/50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:border-indigo-500/20 dark:bg-indigo-500/10 dark:text-indigo-300">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Asisten AI Smart Draft
                        </span>
                    </div>
                </div>
                <div class="rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-50/40 to-brand-100/10 p-5 dark:border-brand-900/30 dark:from-brand-950/20 dark:to-transparent">
                    <h3 class="text-sm font-extrabold text-brand-800 dark:text-brand-400 mb-4 flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-100/80 text-brand-600 dark:bg-brand-900/50 dark:text-brand-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        Informasi Penting &amp; Kontak
                    </h3>
                    <ul class="space-y-3.5 text-xs text-gray-600 dark:text-gray-400">
                        <li class="flex items-start gap-2">
                            <span class="text-brand-500 shrink-0 font-bold">•</span>
                            <span><strong>Batas Pengisian CKP:</strong> Paling lambat tanggal 5 setiap bulan berikutnya.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-brand-500 shrink-0 font-bold">•</span>
                            <span><strong>Batas Unduhan:</strong> Ekspor Excel dibatasi cooldown 1 menit per unduhan.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-brand-500 shrink-0 font-bold">•</span>
                            <span><strong>Dukungan Teknis:</strong> Hubungi Admin Fungsi IPDS jika ada kendala akses.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- ===== TAB PANDUAN PER ROLE (INTERAKTIF ALPINE.JS) ===== --}}
        <section x-data="{ activeRole: 'Anggota Tim' }" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Alur Operasional Berdasarkan Role</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gunakan tab di bawah ini untuk melihat panduan langkah demi langkah sesuai peran aktif Anda.</p>
            </div>

            <!-- Tab Buttons -->
            <div class="mb-6 overflow-x-auto rounded-xl border border-gray-200 bg-gray-50 p-1.5 dark:border-gray-800 dark:bg-gray-950">
                <div class="flex min-w-max gap-1">
                    @foreach ($roleGuides as $guide)
                        <button
                            type="button"
                            @click="activeRole = @js($guide['role'])"
                            class="rounded-lg px-4 py-2.5 text-xs font-bold transition duration-200 cursor-pointer"
                            :class="activeRole === @js($guide['role'])
                                ? 'bg-white text-blue-600 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:text-blue-400 dark:ring-gray-800'
                                : 'text-gray-500 hover:bg-white/50 hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-900/50 dark:hover:text-gray-200'"
                        >
                            {{ $guide['role'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Tab Contents -->
            <div class="relative">
                @foreach ($roleGuides as $guide)
                    <div
                        x-show="activeRole === @js($guide['role'])"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="space-y-6"
                    >
                        <div class="rounded-xl bg-brand-50/50 p-4 dark:bg-brand-950/20 border border-brand-100/30 dark:border-brand-900/30">
                            <h3 class="text-sm font-bold text-brand-800 dark:text-brand-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Ringkasan Peran: {{ $guide['role'] }}
                            </h3>
                            <p class="text-xs text-brand-900/80 dark:text-brand-300 mt-2 leading-relaxed">{{ $guide['summary'] }}</p>
                            
                            <!-- Terkait Menu Badge -->
                            <div class="mt-3 flex flex-wrap gap-2 items-center">
                                <span class="text-[10px] uppercase font-bold text-brand-700/60 dark:text-brand-400/60">Akses Menu:</span>
                                @foreach ($guide['menus'] as $menu)
                                    <span class="rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-800 px-2 py-0.5 text-[10px] font-semibold text-gray-700 dark:text-gray-300">{{ $menu }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                            <!-- Tanggung Jawab -->
                            <div class="rounded-xl border border-gray-100 bg-gray-50/40 p-4 dark:border-gray-800 dark:bg-gray-950/20">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 border-b pb-2">Tanggung Jawab</h4>
                                <ul class="space-y-2.5 text-xs text-gray-700 dark:text-gray-300">
                                    @foreach ($guide['tasks'] as $task)
                                        <li class="flex gap-2">
                                            <span class="text-brand-500 shrink-0">▪</span>
                                            <span class="leading-relaxed">{!! $task !!}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Langkah Kerja -->
                            <div class="rounded-xl border border-gray-100 bg-gray-50/40 p-4 dark:border-gray-800 dark:bg-gray-950/20">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 border-b pb-2">Langkah Kerja</h4>
                                <ol class="space-y-2.5 text-xs text-gray-700 dark:text-gray-300">
                                    @foreach ($guide['steps'] as $idx => $step)
                                        <li class="flex gap-2">
                                            <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-brand-100 text-[10px] font-bold text-brand-700 dark:bg-brand-950 dark:text-brand-400">{{ $idx + 1 }}</span>
                                            <span class="leading-relaxed">{!! $step !!}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>

                            <!-- Contoh Kasus -->
                            <div class="rounded-xl border border-gray-100 bg-gray-50/40 p-4 dark:border-gray-800 dark:bg-gray-950/20">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 border-b pb-2">Contoh Riil</h4>
                                <ul class="space-y-3 text-xs text-gray-700 dark:text-gray-300">
                                    @foreach ($guide['examples'] as $example)
                                        <li class="rounded-lg bg-white p-3 border border-gray-100 dark:bg-gray-900 dark:border-gray-800/60 leading-relaxed shadow-xs">
                                            {!! $example !!}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Catatan Penting -->
                            <div class="rounded-xl border border-gray-100 bg-gray-50/40 p-4 dark:border-gray-800 dark:bg-gray-950/20">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 border-b pb-2">Catatan Penting</h4>
                                <ul class="space-y-2.5 text-xs text-gray-700 dark:text-gray-300">
                                    @foreach ($guide['notes'] as $note)
                                        <li class="flex gap-2 text-amber-700 dark:text-amber-400 font-medium">
                                            <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                            <span class="leading-relaxed">{!! $note !!}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ===== SEKSI: ATURAN BISNIS & VALIDASI KETAT SISTEM ===== --}}
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xs dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-6">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-950/50 dark:text-red-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            Aturan Bisnis &amp; Validasi Ketat Sistem (Hidden Rules)
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Aturan validasi sistem otomatis yang dirancang untuk menjaga integritas data kinerja dan mencegah konflik penjadwalan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Card 1: Bentrok Dinas Luar -->
                <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 transition-all duration-300 hover:-translate-y-1 hover:border-brand-500 hover:shadow-lg hover:shadow-brand-500/5 dark:border-gray-800 dark:bg-gray-950/20 dark:hover:border-brand-400">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600 transition-colors group-hover:bg-brand-100 dark:bg-brand-950/60 dark:text-brand-400 dark:group-hover:bg-brand-950">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                            <h4 class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                                Bentrok Dinas Luar (DL/TL)
                            </h4>
                        </div>
                        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                            Sistem secara ketat <span class="font-semibold text-brand-600 dark:text-brand-400">mencegah tumpang tindih</span> penugasan DL/Translok. Ketika Ketua Tim menambahkan/mengubah penugasan DL/TL, sistem memvalidasi jadwal pegawai secara real-time. Jika pegawai tersebut telah terdaftar di DL/TL lain pada tanggal yang sama, penyimpanan otomatis ditolak.
                        </p>
                    </div>
                    <div class="mt-4 flex items-center gap-1.5 text-[10px] font-semibold text-brand-600 dark:text-brand-400">
                        <span>Validasi Otomatis</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                <!-- Card 2: Grace Period -->
                <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500 hover:shadow-lg hover:shadow-emerald-500/5 dark:border-gray-800 dark:bg-gray-950/20 dark:hover:border-emerald-400">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 transition-colors group-hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-400 dark:group-hover:bg-emerald-950">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <h4 class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                                Tenggang Waktu (Grace Period)
                            </h4>
                        </div>
                        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                            Khusus untuk penugasan bertipe kegiatan <span class="font-semibold text-emerald-600 dark:text-emerald-400">"Pengawasan"</span>, <span class="font-semibold text-emerald-600 dark:text-emerald-400">"Supervisi"</span>, atau <span class="font-semibold text-emerald-600 dark:text-emerald-400">"Perjalanan Dinas"</span>, sistem memberikan kelonggaran pengiriman <span class="font-semibold text-emerald-600 dark:text-emerald-400">H+1 (1 hari tambahan)</span> setelah tanggal selesai penugasan sebelum terhitung terlambat.
                        </p>
                    </div>
                    <div class="mt-4 flex items-center gap-1.5 text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">
                        <span>Kelonggaran H+1 Kerja</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                <!-- Card 3: Transfer Kegiatan -->
                <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 transition-all duration-300 hover:-translate-y-1 hover:border-purple-500 hover:shadow-lg hover:shadow-purple-500/5 dark:border-gray-800 dark:bg-gray-950/20 dark:hover:border-purple-400">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-purple-50 text-purple-600 transition-colors group-hover:bg-purple-100 dark:bg-purple-950/60 dark:text-purple-400 dark:group-hover:bg-purple-950">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </span>
                            <h4 class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                                Transfer CKP Kegiatan
                            </h4>
                        </div>
                        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                            Jika kegiatan dialihkan ke Ketua Tim baru, pembagian nilai CKP didasarkan pada progres riil sebelum tanggal transfer (`transferred_at`). Sub-kegiatan yang <span class="font-semibold text-purple-600 dark:text-purple-400">telah tuntas 100%</span> tetap bernilai untuk Ketua Tim lama. Sisanya beralih ke Ketua Tim baru.
                        </p>
                    </div>
                    <div class="mt-4 flex items-center gap-1.5 text-[10px] font-semibold text-purple-600 dark:text-purple-400">
                        <span>Batas Transferred At</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                <!-- Card 4: Cegah Cicilan Bulan Akhir -->
                <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 transition-all duration-300 hover:-translate-y-1 hover:border-amber-500 hover:shadow-lg hover:shadow-amber-500/5 dark:border-gray-800 dark:bg-gray-950/20 dark:hover:border-amber-400">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition-colors group-hover:bg-amber-100 dark:bg-amber-950/60 dark:text-amber-400 dark:group-hover:bg-amber-950">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            </span>
                            <h4 class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                                Cegah Cicilan Bulan Akhir
                            </h4>
                        </div>
                        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                            Anggota Tim dilarang memilih jenis pengiriman <span class="font-semibold text-amber-600 dark:text-amber-400">"Cicilan"</span> pada bulan terakhir pelaksanaan kegiatan. Sistem mengharuskan pengiriman bulan terakhir berupa tipe <span class="font-semibold text-amber-600 dark:text-amber-400">"Pelunasan"</span> dengan nilai target penuh untuk menutup siklus laporan.
                        </p>
                    </div>
                    <div class="mt-4 flex items-center gap-1.5 text-[10px] font-semibold text-amber-600 dark:text-amber-400">
                        <span>Wajib Pelunasan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                <!-- Card 5: Batal Penerimaan -->
                <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 transition-all duration-300 hover:-translate-y-1 hover:border-red-500 hover:shadow-lg hover:shadow-red-500/5 dark:border-gray-800 dark:bg-gray-950/20 dark:hover:border-red-400">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 transition-colors group-hover:bg-red-100 dark:bg-red-950/60 dark:text-red-400 dark:group-hover:bg-red-950">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </span>
                            <h4 class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                                Konsistensi Batal Penerimaan
                            </h4>
                        </div>
                        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                            Penerimaan ke-N oleh Ketua Tim hanya dapat dibatalkan apabila <span class="font-semibold text-red-500 dark:text-red-400">belum ada pengiriman ke-(N+1)</span> oleh Anggota Tim. Jika pengiriman berikutnya sudah masuk ke sistem, maka riwayat penerimaan sebelumnya dikunci demi konsistensi data.
                        </p>
                    </div>
                    <div class="mt-4 flex items-center gap-1.5 text-[10px] font-semibold text-red-500 dark:text-red-400">
                        <span>Pemberlakuan Kunci Riwayat</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                <!-- Card 6: Deaktivasi Pegawai -->
                <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 hover:shadow-lg hover:shadow-indigo-500/5 dark:border-gray-800 dark:bg-gray-950/20 dark:hover:border-indigo-400">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 transition-colors group-hover:bg-indigo-100 dark:bg-indigo-950/60 dark:text-indigo-400 dark:group-hover:bg-indigo-950">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </span>
                            <h4 class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                                Deaktivasi &amp; Transfer Mandatori
                            </h4>
                        </div>
                        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                            Admin dilarang menonaktifkan akun Ketua Tim jika yang bersangkutan masih memimpin kegiatan aktif yang <span class="font-semibold text-indigo-600 dark:text-indigo-400">belum ditransfer</span>. Semua kegiatan aktif tersebut harus didelegasikan terlebih dahulu demi mencegah terjadinya kekosongan penilai.
                        </p>
                    </div>
                    <div class="mt-4 flex items-center gap-1.5 text-[10px] font-semibold text-indigo-600 dark:text-indigo-400">
                        <span>Wajib Delegasi Mandatori</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== RUMUS DAN LOGIKA PERHITUNGAN KINERJA ===== --}}
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Logika Perhitungan Kinerja &amp; Penilaian</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ulasan detail formula matematika dan pembobotan parameter kinerja yang dipakai sistem secara otomatis.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($calculationCards as $card)
                    <article class="rounded-xl border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-950/40 flex flex-col justify-between shadow-xs">
                        <div>
                            <h3 class="font-bold text-xs text-gray-900 dark:text-white leading-tight mb-2">{{ $card['title'] }}</h3>
                            <div class="rounded-lg bg-white p-3 font-mono text-[10px] font-semibold text-brand-700 border border-gray-200 dark:bg-gray-900 dark:text-brand-300 dark:border-gray-800 leading-normal mb-3">
                                {{ $card['formula'] }}
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">{{ $card['description'] }}</p>
                        </div>
                        <div class="mt-4 rounded-lg bg-white p-3 text-[10px] border dark:bg-gray-900 dark:border-gray-800 text-gray-500 dark:text-gray-400 leading-normal">
                            <span class="font-bold text-gray-700 dark:text-gray-200">Studi Kasus:</span> {{ $card['example'] }}
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- ===== LEGENDA WARNA STATUS PENUGASAN ===== --}}
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Legenda Batas Warna Status Penugasan</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pada halaman Tagihan Kerja, warna garis di tepi kiri (border-left) menunjukkan status pengerjaan penugasan Anggota:</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-950/20 shadow-xs">
                    <div class="w-3.5 h-3.5 rounded-full bg-gray-400 shrink-0"></div>
                    <div class="text-[10px]">
                        <p class="font-bold text-gray-800 dark:text-gray-200">Abu-Abu</p>
                        <p class="text-gray-500">Menunggu Pengiriman</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-950/20 shadow-xs">
                    <div class="w-3.5 h-3.5 rounded-full bg-yellow-400 shrink-0"></div>
                    <div class="text-[10px]">
                        <p class="font-bold text-gray-800 dark:text-gray-200">Kuning</p>
                        <p class="text-gray-500">Menunggu Penerimaan</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-950/20 shadow-xs">
                    <div class="w-3.5 h-3.5 rounded-full bg-orange-400 shrink-0"></div>
                    <div class="text-[10px]">
                        <p class="font-bold text-gray-800 dark:text-gray-200">Jingga</p>
                        <p class="text-gray-500">Menunggu Kirim Ulang</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-950/20 shadow-xs">
                    <div class="w-3.5 h-3.5 rounded-full bg-red-500 shrink-0"></div>
                    <div class="text-[10px]">
                        <p class="font-bold text-gray-800 dark:text-gray-200">Merah</p>
                        <p class="text-gray-500">Belum Diterima / Telat</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-950/20 shadow-xs">
                    <div class="w-3.5 h-3.5 rounded-full bg-blue-400 shrink-0"></div>
                    <div class="text-[10px]">
                        <p class="font-bold text-gray-800 dark:text-gray-200">Biru</p>
                        <p class="text-gray-500">Diterima (Cicilan)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-950/20 shadow-xs">
                    <div class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></div>
                    <div class="text-[10px]">
                        <p class="font-bold text-gray-800 dark:text-gray-200">Hijau</p>
                        <p class="text-gray-500">Diterima (Pelunasan)</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== DUA KOLOM: ISTILAH PENTING & PRAKTIK TERBAIK ===== --}}
        <section class="grid gap-6 md:grid-cols-2">
            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 flex flex-col justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Praktik Terbaik Penggunaan Sistem</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Saran operasional untuk kelancaran integrasi data.</p>
                    
                    <div class="mt-5 space-y-4">
                        <!-- Practice 1: Pelunasan Akumulatif -->
                        <div class="flex gap-3.5 items-start">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-xs text-gray-900 dark:text-white">Pengiriman Tipe Pelunasan</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 leading-relaxed">
                                    Anggota wajib menggunakan tipe <span class="font-semibold text-emerald-600 dark:text-emerald-400">"Pelunasan"</span> di pengiriman terakhir dan memasukkan **akumulasi total pengerjaan** (bukan nilai sisa pengerjaan).
                                </p>
                            </div>
                        </div>

                        <!-- Practice 2: Cek Kalender DL -->
                        <div class="flex gap-3.5 items-start">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-xs text-gray-900 dark:text-white">Pencegahan Bentrok Kalender DL</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 leading-relaxed">
                                    Selalu periksa **Kalender DL/Translok** sebelum menyusun dan menugaskan pekerjaan lapangan untuk menghindari penumpukan jadwal dinas luar pegawai.
                                </p>
                            </div>
                        </div>

                        <!-- Practice 3: Web Push Notification -->
                        <div class="flex gap-3.5 items-start">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-xs text-gray-900 dark:text-white">Aktivasi Web Push Notification</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 leading-relaxed">
                                    Admin dan Pimpinan disarankan mengaktifkan **Web Push** agar notifikasi pengingat todo bulanan dapat masuk langsung ke perangkat pegawai seketika.
                                </p>
                            </div>
                        </div>

                        <!-- Practice 4: Uraian CKP Formal -->
                        <div class="flex gap-3.5 items-start">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-xs text-gray-900 dark:text-white">Gaya Bahasa Uraian CKP</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 leading-relaxed">
                                    Sesuaikan kalimat **Uraian CKP** di menu CKP Saya menggunakan bahasa kerja formal yang sesuai dengan standar penilaian BPS.
                                </p>
                            </div>
                        </div>

                        <!-- Practice 5: Transfer Kegiatan Resmi -->
                        <div class="flex gap-3.5 items-start">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-xs text-gray-900 dark:text-white">Transfer Kegiatan Resmi</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 leading-relaxed">
                                    Lakukan **transfer kegiatan** secara resmi melalui sistem jika ada pergantian penanggung jawab agar pembagian hak CKP terhitung adil.
                                </p>
                            </div>
                        </div>

                        <!-- Practice 6: Filter Sebelum Ekspor -->
                        <div class="flex gap-3.5 items-start">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-xs text-gray-900 dark:text-white">Penyaringan Bulan untuk Ekspor</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 leading-relaxed">
                                    Pimpinan sebaiknya **menyaring bulan spesifik** sebelum mengekspor Excel, karena sistem memblokir ekspor akumulatif tahunan demi efisiensi server.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Glosarium / Istilah Penting</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kamus kecil definisi fungsionalitas yang ada pada sistem Pindang OI.</p>
                <div class="mt-4 divide-y divide-gray-200 text-xs dark:divide-gray-800">
                    @foreach ($importantTerms as $term)
                        <div class="grid gap-2 py-3.5 sm:grid-cols-[140px_1fr] items-start">
                            <dt class="font-extrabold text-brand-700 dark:text-brand-400 uppercase tracking-wide">{{ $term['term'] }}</dt>
                            <dd class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $term['definition'] }}</dd>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>
    </div>
@endsection
