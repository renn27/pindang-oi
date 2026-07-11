<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Panduan Perhitungan Kinerja Pegawai (Anggota) - PINDANG Oi</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        .header {
            border-bottom: 2px solid #2b6cb0;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header td {
            padding: 0;
            vertical-align: middle;
        }
        .header-title {
            font-size: 18pt;
            font-weight: bold;
            color: #2b6cb0;
            margin: 0;
        }
        .header-subtitle {
            font-size: 10pt;
            color: #718096;
            margin: 3px 0 0 0;
        }
        .doc-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a202c;
            margin: 20px 0;
            letter-spacing: 0.5px;
        }
        h2 {
            font-size: 12pt;
            color: #2c5282;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 12px;
        }
        h3 {
            font-size: 11pt;
            color: #2d3748;
            margin-top: 15px;
            margin-bottom: 8px;
        }
        p {
            margin-top: 0;
            margin-bottom: 10px;
            text-align: justify;
        }
        .formula-box {
            background-color: #f7fafc;
            border-left: 4px solid #3182ce;
            padding: 10px 15px;
            margin: 15px 0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
            font-weight: bold;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .info-table th, .info-table td {
            border: 1px solid #cbd5e0;
            padding: 8px 10px;
            font-size: 9.5pt;
            text-align: left;
        }
        .info-table th {
            background-color: #ebf8ff;
            color: #2b6cb0;
            font-weight: bold;
        }
        .info-table td.center {
            text-align: center;
        }
        .bullet-list {
            margin-top: 0;
            margin-bottom: 15px;
            padding-left: 20px;
        }
        .bullet-list li {
            margin-bottom: 6px;
            font-size: 10pt;
            text-align: justify;
        }
        .footer {
            margin-top: 40px;
            font-size: 8pt;
            color: #a0aec0;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        .highlight {
            font-weight: bold;
            color: #2c5282;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="header-title">PINDANG Oi</div>
                    <div class="header-subtitle">Badan Pusat Statistik (BPS) Kabupaten Ogan Ilir</div>
                </td>
                <td style="text-align: right; color: #718096; font-size: 9pt;">
                    Dokumentasi Sistem Penilaian Kinerja
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title">PANDUAN PERHITUNGAN KINERJA PEGAWAI (ANGGOTA)</div>

    <p>Dokumen ini menjelaskan secara transparan metodologi perhitungan nilai kinerja pegawai tingkat staf (anggota tim) di lingkungan BPS Kabupaten Ogan Ilir menggunakan aplikasi PINDANG Oi. Penilaian dihitung secara periodik setiap bulan berdasarkan target, realisasi, kecepatan pengiriman, dan penilaian atasan.</p>

    <h2>1. Gambaran Umum Metode Penilaian</h2>
    <p>Setiap akhir bulan, sistem akan memfilter seluruh penugasan pegawai yang aktif dan berstatus <strong>"Diterima"</strong> pada bulan tersebut. Skor akhir performa pegawai dihitung menggunakan rata-rata dari 4 formula dasar (F1, F2, F3, F4) yang kemudian disesuaikan dengan <strong>Koefisien Beban Kerja</strong> untuk menjamin keadilan bagi pegawai dengan beban tugas ber-volume tinggi.</p>

    <h2>2. Bedah 4 Formula Utama</h2>

    <h3>🔵 F1 - Penyelesaian (Completion Rate)</h3>
    <p>F1 mengukur sejauh mana target fisik yang dibebankan kepada pegawai telah diselesaikan dan diterima. Formula ini <strong>berbasis pada volume/total target penugasan dari pegawai</strong>, bukan dari jumlah penugasannya.</p>
    
    <div class="formula-box">
        F1 = (d / c) * (b_efektif / a) * 100
    </div>

    <p><strong>Variabel Penjelas:</strong></p>
    <ul class="bullet-list">
        <li><span class="highlight">a</span>: Total target penugasan yang dibebankan secara personal kepada pegawai pada bulan aktif.</li>
        <li><span class="highlight">c</span>: Total target penugasan kumulatif seluruh pegawai di kantor pada bulan aktif.</li>
        <li><span class="highlight">b_efektif</span>: Bobot penyelesaian efektif pegawai, dihitung dari: <br>
            <code>b_efektif = progress_pelunasan + b_efektif_cicilan</code>
        </li>
        <li><span class="highlight">b_efektif_cicilan</span>: Kontribusi kuadratik cicilan berdasarkan sisa target per kegiatan, dirumuskan: <br>
            <code>b_efektif_cicilan = SUM(jumlah_dikirim^2 / target)</code>. Logika kuadratik ini memastikan pegawai yang mencicil lebih banyak (misal 8 dari 10) mendapatkan skor jauh lebih proporsional dibanding yang baru mencicil sedikit (misal 1 dari 10).
        </li>
        <li><span class="highlight">d</span>: Sisa kapasitas global tim yang berhasil diamankan setelah menghitung sisa beban pegawai ini: <br>
            <code>d = max(0, c - a + b_efektif)</code>
        </li>
    </ul>

    <h3>🟡 F2 - Kecepatan (Speed Rate)</h3>
    <p>F2 menilai ketepatan waktu pengiriman penugasan bertipe <strong>Pelunasan</strong> yang berstatus Diterima dibandingkan dengan masa rentang deadline penugasan. Penugasan bertipe Cicilan tidak diikutkan dalam F2.</p>
    
    <div class="formula-box">
        Jika Tepat Waktu atau Lebih Cepat (lama_pengiriman <= lama_rentang):<br>
        &nbsp;&nbsp;F2 = 80% + ((lama_rentang - lama_pengiriman) / lama_rentang) * 20%<br><br>
        Jika Terlambat (lama_pengiriman > lama_rentang):<br>
        &nbsp;&nbsp;keterlambatan_relatif = ((lama_pengiriman - lama_rentang) / lama_rentang) * 10<br>
        &nbsp;&nbsp;F2 = max(70%, 80% - min(10%, keterlambatan_relatif))
    </div>

    <p>Dengan formula ini, skor minimum kecepatan pengiriman dibatasi paling rendah adalah <strong>70%</strong> (floor limit) untuk keterlambatan yang sangat ekstrem.</p>

    <div class="page-break"></div>

    <h3>🟢 F3 - RR Kirim (Response Rate)</h3>
    <p>F3 mengukur rata-rata response rate kualitas pengiriman laporan yang diunggah pegawai. Setiap penugasan diboboti secara proporsional berdasarkan status penyelesaiannya:</p>
    <ul class="bullet-list">
        <li>Penugasan bertipe <strong>Pelunasan</strong>: bobot = 1.0 (penuh).</li>
        <li>Penugasan bertipe <strong>Cicilan</strong>: bobot = <code>jumlah_dikirim / target</code>.</li>
    </ul>
    
    <div class="formula-box">
        F3 = SUM(rr_kirim * bobot) / n_penugasan
    </div>

    <h3>🔴 F4 - Rating Kirim (Quality Review)</h3>
    <p>F4 mengukur rata-rata kepuasan dan nilai kualitas yang diberikan oleh Ketua Tim (Katim) atau atasan penilai dalam skala 1 hingga 5 bintang. Konversi nilai ke skala 100% dilakukan dengan mengalikan rating dengan angka 20.</p>
    
    <div class="formula-box">
        F4 = SUM(rating_kirim * 20 * bobot) / n_penugasan
    </div>

    <h2>3. Akumulasi Skor dan Koefisien Beban</h2>
    
    <p>Setelah mendapatkan nilai F1, F2, F3, dan F4 (masing-masing berskala 0% s.d. 100%), sistem menghitung <strong>Rata-rata Base</strong>:</p>
    <div class="formula-box">
        Rata-rata Base = (F1 + F2 + F3 + F4) / 4
    </div>

    <h3>⚖️ Koefisien Beban Kerja (Workload Adjustment)</h3>
    <p>Untuk menyeimbangkan beban kerja, sistem membandingkan total target pribadi pegawai dengan rata-rata target pegawai kantor:</p>
    <div class="formula-box">
        avg_target = total_target_seluruh_tim / jumlah_pegawai_aktif<br>
        koefisien_beban = min(1.15, max(0.85, target_pegawai / avg_target))
    </div>

    <p><strong>Penerapan Skor Akhir (Rata-rata Final):</strong></p>
    <ul class="bullet-list">
        <li><strong>Jika beban kerja di atas rata-rata (koefisien_beban >= 1.0)</strong>:<br>
            Pegawai mendapatkan bonus proporsional yang dibatasi agar skor akhir tidak melampaui 100%.<br>
            <code>rata_rata_final = rata_rata_base + min(rata_rata_base * (koefisien_beban - 1.0), 100 - rata_rata_base)</code>
        </li>
        <li><strong>Jika beban kerja di bawah rata-rata (koefisien_beban < 1.0)</strong>:<br>
            Pegawai mendapatkan penalti proporsional karena memikul tanggung jawab volume/total target penugasan yang lebih ringan.<br>
            <code>rata_rata_final = max(0, rata_rata_base * koefisien_beban)</code>
        </li>
    </ul>

    <h2>4. Urutan Perangkingan</h2>
    <p>Apabila terdapat lebih dari satu pegawai yang mendapatkan nilai Rata-rata Final yang sama (contohnya sama-sama bernilai 100.00%), sistem akan menentukan peringkat berdasarkan urutan prioritas berikut:</p>
    <table class="info-table">
        <thead>
            <tr>
                <th style="width: 15%;">Prioritas</th>
                <th style="width: 45%;">Kriteria Penentu</th>
                <th style="width: 40%;">Penjelasan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center">1</td>
                <td><strong>Pegawai Aktif</strong></td>
                <td>Pegawai yang memiliki penugasan diutamakan di atas pegawai non-aktif/tanpa tugas.</td>
            </tr>
            <tr>
                <td class="center">2</td>
                <td><strong>Rata-rata Final Tertinggi</strong></td>
                <td>Diurutkan secara menurun (descending) berdasarkan skor akhir.</td>
            </tr>
            <tr>
                <td class="center">3</td>
                <td><strong>Target Penugasan Terbanyak</strong></td>
                <td>Pegawai dengan akumulasi target penugasan (<code>target_pegawai</code>) terbanyak diposisikan di atas.</td>
            </tr>
            <tr>
                <td class="center">4</td>
                <td><strong>Nama Pegawai (Abjad)</strong></td>
                <td>Diurutkan secara alfabetis (A-Z) jika seluruh kriteria di atas bernilai sama.</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Aplikasi PINDANG Oi &copy; 2026 BPS Kabupaten Ogan Ilir. Seluruh data perhitungan dilindungi sistem dan bersifat transparan.
    </div>

</body>
</html>
