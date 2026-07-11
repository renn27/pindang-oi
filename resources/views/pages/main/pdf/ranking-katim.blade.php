<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Panduan Perhitungan Kinerja Ketua Tim (Katim) - PINDANG Oi</title>
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
            border-bottom: 2px solid #319795;
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
            color: #319795;
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
            border-left: 4px solid #319795;
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
            background-color: #e6fffa;
            color: #319795;
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

    <div class="doc-title">PANDUAN PERHITUNGAN KINERJA KETUA TIM (KATIM)</div>

    <p>Dokumen ini menjelaskan secara transparan metodologi perhitungan nilai kinerja tingkat Ketua Tim (Katim) di lingkungan BPS Kabupaten Ogan Ilir menggunakan aplikasi PINDANG Oi. Berbeda dengan pegawai biasa (staf/anggota), penilaian Ketua Tim murni diukur dari performa pengawasan kegiatan tim yang dipimpinnya.</p>

    <h2>1. Gambaran Umum Metode Penilaian</h2>
    <p>Penilaian untuk Ketua Tim berfokus pada kualitas pengawasan dan pembinaan anggota tim di bawah sub-kegiatan yang diketuainya. Skor akhir Katim didapatkan dari nilai <strong>F5 (Kinerja Pengawasan)</strong> sebagai skor dasar, yang kemudian dikalikan dengan <strong>Koefisien Beban Tim</strong> sebagai faktor penyeimbang (fairness) volume target tim.</p>

    <h2>2. Rumus Nilai Dasar (Base Score - F5)</h2>
    <p>Skor dasar (Base Score) Ketua Tim diambil 100% dari nilai pengawasan timnya (F5). Formula ini mengukur performa akumulasi seluruh anggota tim di bawah tanggung jawab Katim pada bulan aktif yang berstatus <strong>"Diterima"</strong>.</p>
    
    <div class="formula-box">
        Base Score (Rata-rata Base) = F5
    </div>

    <p>F5 dihitung berdasarkan rata-rata dari dua komponen kualitas tim berikut:</p>
    <ul class="bullet-list">
        <li><span class="highlight">RR Terima (Response Rate)</span>: Rata-rata persentase response rate pengiriman anggota tim yang sudah berstatus 'Diterima' di bawah sub-kegiatan yang dipimpin Katim.</li>
        <li><span class="highlight">Rating Terima (Quality Score)</span>: Rata-rata nilai rating bintang (skala 1-5 bintang) yang dikonversi ke skala 100% (rating dikali 20) untuk seluruh pengiriman berstatus 'Diterima' di bawah sub-kegiatan Katim.</li>
    </ul>

    <h2>3. Koefisien Beban Kerja Tim (Fairness Adjustment)</h2>
    <p>Untuk memberikan aspek keadilan bagi Katim yang memimpin tim dengan volume/total target penugasan yang sangat besar, sistem menerapkan faktor penyesuaian **Koefisien Beban Tim**:</p>
    
    <div class="formula-box">
        rasio_beban = total_target_tim / avg_target_tim<br>
        koefisien_beban = min(1.15, max(0.85, rasio_beban))
    </div>

    <p><strong>Variabel Penjelas:</strong></p>
    <ul class="bullet-list">
        <li><span class="highlight">total_target_tim</span>: Jumlah total seluruh target penugasan dari semua anggota tim di bawah sub-kegiatan yang dipimpin oleh Katim tersebut (pada bulan evaluasi).</li>
        <li><span class="highlight">avg_target_tim</span>: Rata-rata total target tim di kantor (rata-rata dari total target seluruh Katim aktif di bulan tersebut).</li>
        <li><span class="highlight">koefisien_beban</span>: Faktor penyesuaian beban kerja tim yang diperoleh dari rasio beban kerja tim terhadap rata-rata kantor.</li>
        <li><span class="highlight">Batasan Koefisien</span>: Koefisien dibatasi paling rendah <strong>0.85</strong> (penalti minimum) dan paling tinggi <strong>1.15</strong> (bonus maksimum).</li>
    </ul>

    <h2>4. Perhitungan Skor Akhir (Rata-rata Final)</h2>
    <p>Bonus atau penalti beban kerja tim diaplikasikan langsung pada nilai rata-rata base (F5) untuk menghasilkan skor akhir peringkat:</p>
    
    <ul class="bullet-list">
        <li><strong>Jika beban kerja tim di atas rata-rata kantor (koefisien_beban >= 1.0)</strong>:<br>
            Katim mendapatkan bonus beban kerja tambahan, yang dibatasi agar skor akhir tidak melampaui batas maksimum 100.00%.<br>
            <code>rata_rata_final = rata_rata_base + min(rata_rata_base * (koefisien_beban - 1.0), 100 - rata_rata_base)</code>
        </li>
        <li><strong>Jika beban kerja tim di bawah rata-rata kantor (koefisien_beban < 1.0)</strong>:<br>
            Katim mendapatkan penalti proporsional karena memikul tanggung jawab pengawasan volume/total target penugasan yang lebih ringan.<br>
            <code>rata_rata_final = max(0, rata_rata_base * koefisien_beban)</code>
        </li>
    </ul>

    <h2>5. Urutan Penentu Rangking</h2>
    <p>Apabila terdapat lebih dari satu Ketua Tim yang mendapatkan nilai Rata-rata Final yang sama, urutan peringkat ditentukan berdasarkan prioritas kriteria berikut:</p>
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
                <td><strong>Katim Aktif</strong></td>
                <td>Katim yang memimpin sub-kegiatan aktif pada bulan tersebut diutamakan di atas Katim tanpa tugas aktif.</td>
            </tr>
            <tr>
                <td class="center">2</td>
                <td><strong>Rata-rata Final Tertinggi</strong></td>
                <td>Diurutkan secara menurun (descending) berdasarkan skor akhir.</td>
            </tr>
            <tr>
                <td class="center">3</td>
                <td><strong>Total Target Tim Terbanyak</strong></td>
                <td>Katim dengan total beban kerja seluruh anggotanya (<code>total_target_tim</code>) terbanyak diposisikan di atas.</td>
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
