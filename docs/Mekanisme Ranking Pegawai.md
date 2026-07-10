# 📊 Mekanisme Ranking Pegawai — Dokumentasi Teknis Lengkap

> **Terakhir diperbarui:** Mei 2026  
> **File referensi:** `app/Services/DashboardAnalyticsService.php`  
> **Perubahan utama:** Perhitungan F1–F4 kini hanya melibatkan penugasan yang sudah berstatus **"Diterima"** di bulan aktif (bukan semua pengiriman).

---

## 1. 🧩 Gambaran Besar — Rumus Ini Ngapain?

Sistem ini menghitung **skor performa pegawai** secara adil dan transparan setiap bulan. Masalah yang diselesaikan:

| Masalah Lama | Solusi Sistem Baru |
|---|---|
| Pegawai volume tinggi selalu menang | Koefisien beban menyeimbangkan target besar vs kecil |
| Pengiriman belum diverifikasi ikut dihitung | Hanya penugasan **"Diterima"** yang masuk perhitungan |
| Cicilan dan Pelunasan diperlakukan sama | Cicilan diberi bobot 0.5 (setengah nilai Pelunasan) |
| Skor bisa melebihi 100% | Fairness bonus dibatasi agar total ≤ 100% |

**Alur kerja besar:**

```
Data Penugasan Aktif Bulan X
        ↓
Filter: hanya bulan_pengiriman = bulan aktif & status Diterima
        ↓
Hitung 4 Formula (F1, F2, F3, F4)
        ↓
Rata-rata Base = (F1+F2+F3+F4) / 4
        ↓
Terapkan Koefisien Beban (bonus/penalti beban kerja)
        ↓
Rata-rata Final = Skor Akhir Ranking
```

---

## 2. 🔬 Bedah Masing-Masing Formula

### 🔵 F1 — Penyelesaian (Seberapa Banyak yang Dikerjakan?)

**Pertanyaan:** Dari semua target yang harus diselesaikan bulan ini, berapa persen yang sudah dikerjakan (dan sudah Diterima)?

> ⚠️ **F1 menghitung berdasarkan nilai TARGET (angka dokumen/pekerjaan), bukan jumlah penugasan.**  
> Penugasan dengan target = 5 berkontribusi 5× lebih besar ke denominator daripada penugasan target = 1.

---

#### Variabel F1

| Simbol | Nama | Penjelasan |
|---|---|---|
| `c` | Total target tim | Jumlah total target seluruh penugasan aktif bulan ini (semua pegawai) |
| `a` | Target pegawai | Jumlah total target yang dibebankan ke pegawai ini |
| `progress_pelunasan` | Progress Lunas | Jumlah dokumen yang dikirim dengan tipe Pelunasan & sudah Diterima bulan ini |
| `progress_cicilan` | Progress Cicilan | Jumlah dokumen yang dikirim dengan tipe Cicilan & sudah Diterima bulan ini |
| `b_efektif_cicilan` | Bobot efektif cicilan | `SUM(jumlah_dikirim² / target)` per penugasan — makin besar cicilan relatif ke target, makin besar nilainya |
| `b_efektif` | Bobot efektif total | `progress_pelunasan + b_efektif_cicilan` |
| `d` | Sisa kapasitas global | Kapasitas tim yang "terbebas" setelah memperhitungkan sisa beban pegawai ini |

---

#### Memahami `b_efektif` — Bobot Proporsional Cicilan

```
b_efektif_cicilan = SUM( jumlah_dikirim × (jumlah_dikirim / target) )
                 = SUM( jumlah_dikirim² / target )   ← per penugasan

b_efektif = progress_pelunasan + b_efektif_cicilan
```

Bobot cicilan bukan flat 0.5, melainkan **proporsional** sesuai seberapa jauh cicilan sudah mengisi target:

| Cicilan | Target | Bobot (`j/t`) | Kontribusi ke b_efektif (`j × j/t = j²/t`) |
|---|---|---|---|
| 1 | 10 | 0.10 | `1 × 0.10 = 0.10` |
| 3 | 10 | 0.30 | `3 × 0.30 = 0.90` |
| 5 | 10 | 0.50 | `5 × 0.50 = 2.50` |
| 8 | 10 | 0.80 | `8 × 0.80 = 6.40` |
| 10 | 10 | 1.00 | `10 × 1.00 = 10.0` ← setara Pelunasan |

**Kenapa kuadratik (`j²/t`)?**  
Karena bobot *dan* progress keduanya berbanding lurus dengan `j`. Pegawai yang sudah mengirim 8 dari 10 (80%) mendapat kredit jauh lebih besar dari yang baru kirim 1 dari 10 (10%). Ini lebih adil dari flat 0.5 yang menyamakan cicilan 1/10 dengan cicilan 9/10.

**Konsistensi:** F3 dan F4 sudah lama pakai bobot `jumlah/target` untuk Cicilan. Perubahan ini menyeragamkan F1 dengan logika yang sama.

---

#### Memahami `d` — Logika di Balik Rumusnya

Intuisi:
> *"d itu sisa dari target global dikurangi bagian yang belum diselesaikan pegawai ini, jadi harusnya `c - (a - progress)`"*

Benar secara konsep. Perhatikan:

```
d = max(0, c - a + b_efektif)
   = max(0, c - (a - b_efektif))
             └──────────────────┘
             "beban yang belum tuntas" (target - yang sudah efektif dikerjakan)
```

- `a - b_efektif` = **beban sisa** (target yang masih "hutang")
- `c - (a - b_efektif)` = **kapasitas tim dikurangi beban sisa** = ruang yang "terbebas"

Perbedaan dari harapan awal hanya pada apa yang dimaksud "yang sudah dikerjakan":

| Versi | `d` |
|---|---|
| Raw progress (3) | `50 - (10 - 3) = 43` |
| Flat 0.5 (lama) | `50 - (10 - 1.5) = 41.5` |
| **Proporsional `j²/t` (sekarang)** | `50 - (10 - 0.9) = 40.9` |

Perbedaan ini **sengaja** — cicilan 3/10 bukan setengah selesai (flat 0.5=1.5), melainkan hanya 30% × 3 = 0.9 yang benar-benar dianggap tuntas.

---

#### Rumus Lengkap F1

```
-- Per penugasan Cicilan:
bobot_cicilan        = jumlah_dikirim / target
kontribusi_b_cicilan = jumlah_dikirim × bobot_cicilan
                     = jumlah_dikirim² / target

-- Akumulasi pegawai:
b_efektif_cicilan = SUM(jumlah_dikirim² / target)   ← semua penugasan Cicilan
b_efektif         = progress_pelunasan + b_efektif_cicilan

d  = max(0, c - (a - b_efektif))

F1 = (d / c) × (b_efektif / a) × 100
```

**Dua komponen F1:**

| Komponen | Arti |
|---|---|
| `d / c` | Seberapa besar kapasitas global yang "terbebas" dari beban sisa pegawai ini |
| `b_efektif / a` | Rasio penyelesaian efektif lokal pegawai |

Keduanya dikalikan → skor yang mempertimbangkan **kinerja lokal** (b/a) sekaligus **posisi relatif terhadap tim** (d/c).

---

#### Contoh Angka Lengkap

Setup: `c = 50`, `a = 10`, target per penugasan = 10, cicilan = 3

```
bobot_cicilan     = 3 / 10          = 0.3
b_efektif_cicilan = 3 × 0.3         = 0.9
b_efektif         = 0 + 0.9         = 0.9
d                 = 50 - 10 + 0.9   = 40.9
F1                = (40.9/50) × (0.9/10) × 100
                 = 0.818 × 0.09 × 100
                 = 7.36
```

Bandingkan jika cicilan 8 dari 10:

```
b_efektif_cicilan = 8 × (8/10)      = 6.4
b_efektif         = 0 + 6.4          = 6.4
d                 = 50 - 10 + 6.4   = 46.4
F1                = (46.4/50) × (6.4/10) × 100
                 = 0.928 × 0.64 × 100
                 = 59.4
```

Jika semua 10 dokumen Pelunasan (selesai penuh):

```
b_efektif = 10 + 0       = 10
d         = 50 - 10 + 10 = 50
F1        = (50/50) × (10/10) × 100 = 100
```

**Edge case:**
- Jika `a = 0` atau `c = 0` → F1 = 0 (tidak ada target → tidak ada skor)
- `d` tidak pernah negatif karena ada `max(0, ...)`
- Jika `target = 0` pada penugasan Cicilan → kontribusi = 0 (dilindungi `AND penugasans.target > 0`)


---

### 🟡 F2 — Kecepatan (Seberapa Cepat Menyelesaikan?)

**Pertanyaan:** Apakah pegawai mengirim Pelunasan lebih awal atau terlambat dari deadline?

**Hanya berlaku untuk tipe `Pelunasan`**. Cicilan tidak ikut F2.

**Variabel:**
- `lama_rentang` = jarak hari dari `tanggal_mulai` ke `tanggal_selesai` (minimal 1 hari)
- `lama_pengiriman` = jarak hari dari `tanggal_mulai` ke `tanggal_pengiriman`

**Rumus:**

```
Jika lama_pengiriman ≤ lama_rentang (tepat waktu atau lebih cepat):
  F2 = 80 + ((lama_rentang - lama_pengiriman) / lama_rentang) × 20

Jika lama_pengiriman > lama_rentang (terlambat):
  keterlambatan_relatif = ((lama_pengiriman - lama_rentang) / lama_rentang) × 10
  F2 = max(70, 80 - min(10, keterlambatan_relatif))
```

**Interpretasi skor:**

| Kondisi | Rentang Skor F2 |
|---|---|
| Kirim tepat di hari terakhir deadline | 80.0 |
| Kirim jauh sebelum deadline | 80.0 – 100.0 |
| Kirim 1 hari setelah deadline | ~79 (mendekati 70) |
| Terlambat sangat jauh | 70.0 (floor minimum) |

**Catatan:** F2 adalah rata-rata dari seluruh penugasan Pelunasan yang Diterima bulan ini.

---

### 🟢 F3 — RR Kirim (Rata-Rata Kualitas Kiriman)

**Pertanyaan:** Berapa skor RR (Rencana vs Realisasi) rata-rata pegawai, dengan mempertimbangkan seberapa besar kontribusi setiap penugasan?

**Bobot per penugasan:**
- `Pelunasan` → bobot = 1.0 (penuh)
- `Cicilan` → bobot = `jumlah_dikirim / target` (proporsional)

**Rumus:**

```
Kontribusi RR tiap penugasan = rr_kirim × bobot

F3 = SUM(semua kontribusi RR) / COUNT(penugasan yang ada pengiriman Diterima)
```

**Artinya:** Cicilan yang baru mengirim 3 dari 10 target hanya menyumbang 30% dari nilai RR-nya ke rata-rata tim.

---

### 🔴 F4 — Rating Kirim (Kualitas Penilaian Atasan)

**Pertanyaan:** Berapa skor rating rata-rata dari atasan, dengan bobot yang sama seperti F3?

**Skala rating:** 1–5 bintang → dikali 20 untuk jadi 0–100

**Bobot per penugasan:** sama dengan F3 (Pelunasan = 1.0, Cicilan = proporsional)

**Rumus:**

```
Kontribusi Rating tiap penugasan = rating_kirim × 20 × bobot

F4 = SUM(semua kontribusi Rating) / COUNT(penugasan yang ada pengiriman Diterima)
```

**Contoh:** Rating 4 bintang dengan bobot penuh = 4 × 20 × 1.0 = 80 poin.

---

## 3. ➕ Rumus Akumulasi — Skor Akhir

### 3.1 Rata-rata Base

```
rata_rata_base = (F1 + F2 + F3 + F4) / 4
```

Ini adalah skor murni sebelum koreksi beban kerja.

---

### 3.2 Koefisien Beban

**Pertanyaan:** Apakah pegawai ini menanggung beban lebih besar atau lebih kecil dari rata-rata?

```
avg_target = total_target_semua_tim / jumlah_pegawai_aktif

koefisien_beban = LEAST(1.15, GREATEST(0.85, target_pegawai / avg_target))
```

| Koefisien | Artinya |
|---|---|
| 1.15 | Beban maksimum (dibatasi agar tidak terlalu besar) |
| 1.0 | Beban tepat rata-rata |
| 0.85 | Beban minimum (dibatasi agar tidak terlalu kecil) |

---

### 3.3 Rata-rata Final (Skor Akhir)

```
Jika koefisien_beban ≥ 1.0 (beban di atas rata-rata → dapat bonus):
  bonus_max   = rata_rata_base × (koefisien_beban - 1.0)
  ruang_ke_100 = max(0, 100 - rata_rata_base)
  bonus_aktual = min(bonus_max, ruang_ke_100)
  rata_rata_final = rata_rata_base + bonus_aktual

Jika koefisien_beban < 1.0 (beban di bawah rata-rata → kena penalti):
  rata_rata_final = max(0, rata_rata_base × koefisien_beban)
```

**Aturan penting:**
- Skor tidak pernah melebihi 100%
- Skor tidak pernah kurang dari 0%
- Bonus hanya bisa mengisi "ruang kosong" menuju 100

---

## 4. 🔧 Bedah Fungsi & Query di DashboardAnalyticsService

### 4.1 `rankInit($month, $year)`

**Fungsi:** Inisialisasi parameter awal sebelum ranking dihitung.

```php
$bf = sprintf('%04d-%02d', $year, $month); // contoh: "2026-04"
```

- `$bf` = "bulan filter" dalam format `YYYY-MM`, dipakai untuk mencocokkan `bulan_pengiriman`
- Memanggil `getGlobalStats()` dan mengembalikan array `[$month, $year, $bf, $gs]`

---

### 4.2 `getGlobalStats($bf, $year, $month)`

**Fungsi:** Menghitung statistik global tim untuk bulan tersebut.

**Query SQL yang dijalankan:**

```sql
SELECT
  COUNT(DISTINCT id_penugasan) as tot,
  COALESCE(SUM(target), 0) as sumT,
  COUNT(DISTINCT id_anggota) as tot_pegawai
FROM penugasans
WHERE deleted_at IS NULL
  AND (YEAR(tanggal_mulai)*12 + MONTH(tanggal_mulai)) <= [am]
  AND (YEAR(tanggal_selesai)*12 + MONTH(tanggal_selesai)) >= [am]
```

**Penjelasan tiap bagian:**

| Bagian | Arti |
|---|---|
| `YEAR(tanggal_mulai)*12 + MONTH(tanggal_mulai) <= am` | Penugasan sudah mulai sebelum atau di bulan ini |
| `YEAR(tanggal_selesai)*12 + MONTH(tanggal_selesai) >= am` | Penugasan belum selesai sampai bulan ini |
| `COALESCE(SUM(target), 0)` | Total target tim; jika NULL → 0 |
| `COUNT(DISTINCT id_anggota)` | Berapa pegawai aktif di bulan ini |

> **Kenapa `YEAR*12 + MONTH`?**  
> Teknik ini mengubah tanggal menjadi angka serial bulan, agar bisa dibandingkan dengan mudah tanpa ambiguitas hari.

**Output:**
```php
[
  'total_penugasan_semua' => int,
  'sum_target_semua'      => float,   // c
  'avg_target_bulan'      => float,   // avg = c / jumlah_pegawai
]
```

---

### 4.3 `buildLatestDiterimaSubquery($bf)`

**Fungsi:** Mengambil **satu pengiriman terbaru per penugasan per bulan** yang sudah berstatus `Diterima`.

**Ini adalah filter utama yang memastikan hanya data "Diterima" yang masuk perhitungan.**

**Query (disederhanakan):**

```sql
-- Subquery: ambil created_at terbaru per penugasan per bulan
SELECT id_penugasan, bulan_pengiriman, MAX(created_at) as lc
FROM pengirimans
WHERE deleted_at IS NULL
GROUP BY id_penugasan, bulan_pengiriman

-- Query utama: join ke subquery untuk ambil baris yang paling baru
SELECT pengirimans.*, penerimaans.status as status_penerimaan
FROM pengirimans
JOIN [subquery above] lat
  ON pengirimans.id_penugasan     = lat.id_penugasan
 AND pengirimans.bulan_pengiriman = lat.bulan_pengiriman
 AND pengirimans.created_at       = lat.lc
JOIN penerimaans ON penerimaans.id_pengiriman = pengirimans.id_pengiriman
WHERE penerimaans.status = 'Diterima'
  AND pengirimans.tipe_pengiriman IN ('Cicilan', 'Pelunasan')
  AND pengirimans.bulan_pengiriman = '2026-04'  -- bulan aktif
  AND pengirimans.deleted_at IS NULL
```

**Fungsi `joinSub`:** Menggabungkan sebuah subquery sebagai tabel virtual. Ini lebih efisien daripada membuat VIEW terpisah karena subquery langsung di-embed ke dalam query induk.

**Hasil:** Tabel virtual `lp` yang berisi hanya pengiriman terverifikasi bulan aktif.

---

### 4.4 `buildRankBaseQuery($bf, $year, $month, $gs)`

**Fungsi:** Query inti yang menghitung semua komponen F1–F4 + koefisien untuk setiap pegawai.

#### Struktur Query

```sql
-- INNER QUERY: hitung semua metrik per pegawai
SELECT
  pegawais.id_pegawai, pegawais.nama_pegawai, pegawais.photo,

  -- Jumlah penugasan aktif (termasuk yang belum ada kiriman)
  COUNT(DISTINCT penugasans.id_penugasan) AS total_penugasan,

  -- Penugasan yang sudah ada pengiriman Diterima bulan ini
  COUNT(DISTINCT lp.id_penugasan) AS total_penugasan_dikerjakan,

  -- Penugasan yang sudah Pelunasan (selesai penuh)
  COUNT(DISTINCT CASE WHEN lp.tipe='Pelunasan' THEN penugasans.id_penugasan END) AS total_selesai,

  -- Penugasan yang masih Cicilan
  COUNT(DISTINCT CASE WHEN lp.tipe='Cicilan' THEN penugasans.id_penugasan END) AS total_cicilan_diterima,

  -- Total target pegawai
  COALESCE(SUM(penugasans.target), 0) AS target_pegawai,

  -- Progress dari Pelunasan
  COALESCE(SUM(CASE WHEN lp.tipe='Pelunasan' THEN lp.jumlah_dikirim ELSE 0 END), 0) AS progress_pelunasan,

  -- Progress dari Cicilan
  COALESCE(SUM(CASE WHEN lp.tipe='Cicilan' THEN lp.jumlah_dikirim ELSE 0 END), 0) AS progress_cicilan,

  -- Rating rata-rata (hanya penugasan yang ada lp)
  COALESCE(AVG(CASE WHEN lp.tipe IS NOT NULL THEN lp.rating_kirim END), 0) AS rating_kirim_avg,

  -- F1, F2, F3, F4, koefisien (lihat penjelasan masing-masing di atas)
  ...

FROM pegawais
LEFT JOIN penugasans ON pegawais.id_pegawai = penugasans.id_anggota
  AND penugasans.deleted_at IS NULL
  AND (YEAR(tanggal_mulai)*12+MONTH(tanggal_mulai)) <= am
  AND (YEAR(tanggal_selesai)*12+MONTH(tanggal_selesai)) >= am
LEFT JOIN [buildLatestDiterimaSubquery] lp ON penugasans.id_penugasan = lp.id_penugasan
GROUP BY pegawais.id_pegawai, nama_pegawai, photo

-- OUTER QUERY: hitung rata-rata dan final score
SELECT ranked.*,
  (f1+f2+f3+f4)/4 AS rata_rata_base,
  [rumus bonus/penalti] AS rata_rata_final
FROM [inner] AS ranked
ORDER BY
  CASE WHEN total_penugasan=0 THEN 1 ELSE 0 END,  -- pegawai tanpa penugasan di bawah
  rata_rata_final DESC,
  total_selesai DESC,
  nama_pegawai ASC
```

**Penjelasan fungsi SQL penting:**

| Fungsi | Penjelasan |
|---|---|
| `COALESCE(expr, default)` | Jika `expr` bernilai NULL, gunakan `default`. Contoh: `COALESCE(SUM(target), 0)` → jika pegawai tidak punya penugasan, hasilnya 0 bukan NULL |
| `LEAST(a, b)` | Ambil nilai terkecil dari a dan b. Dipakai untuk membatasi koefisien max 1.15 dan penalty F2 max 10 |
| `GREATEST(a, b)` | Ambil nilai terbesar dari a dan b. Dipakai untuk floor minimum (koefisien min 0.85, F2 min 70) |
| `NULLIF(expr, 0)` | Ubah 0 menjadi NULL, untuk menghindari pembagian dengan 0 (division by zero) |
| `AVG(CASE WHEN ... THEN val END)` | AVG bersyarat — hanya merata-ratakan baris yang memenuhi kondisi, baris lain diabaikan (NULL tidak ikut AVG) |
| `COUNT(DISTINCT ...)` | Hitung unik, mencegah duplikasi saat JOIN menghasilkan banyak baris |
| `joinSub` | Laravel: gabungkan subquery sebagai tabel virtual (alias `lp`) |
| `leftJoin` | Join yang mempertahankan semua baris kiri meski tidak ada pasangan di kanan (pegawai tanpa penugasan tetap muncul) |

---

### 4.5 `buildDetailsQuery($bf, $year, $month, $ids)`

**Fungsi:** Mengambil data detail per penugasan (bukan per pegawai) untuk ditampilkan di modal breakdown.

- Menggunakan `buildLatestDiterimaSubquery` yang sama
- Filter: penugasans yang aktif di bulan tersebut, dan hanya untuk pegawai yang ada di halaman (pakai `$ids`)
- Melakukan kalkulasi PHP (bukan SQL) untuk: `bobot_parsial`, `kontribusi_rr`, `kontribusi_rating`, `skor_f2`
- Hasilnya di-`groupBy('id_anggota')` → mudah di-lookup per pegawai

---

### 4.6 `decorateRankItem($item, $details, $gs)`

**Fungsi:** Menambahkan properti display ke setiap baris pegawai setelah diambil dari DB.

Yang dilakukan:
1. Hitung bintang penuh/setengah/kosong dari `rating_kirim_avg`
2. Format ulang nilai F1–F4 dan skor final
3. Attach detail penugasan dari `buildDetailsQuery`
4. Hitung `breakdown_formula` lengkap untuk modal:
   - nilai masing-masing F
   - `bonus_aktual` = berapa bonus yang benar-benar diberikan
   - `ruang_ke_100` = sisa skor menuju 100%

---

### 4.7 `rankPegawai()` vs `rankPegawaiAll()`

| | `rankPegawai()` | `rankPegawaiAll()` |
|---|---|---|
| Output | Paginated (LengthAwarePaginator) | Collection lengkap |
| Dipakai oleh | Dashboard tabel utama | Export / kalkulasi global |
| Cara loop | `->through()` | `->map()` |

---

## 5. 🎯 Ilustrasi Skenario — Pegawai Bulan April–Juni

### Setup Skenario

- **Pegawai:** Budi
- **Penugasan:** 1 penugasan, tanggal mulai **1 April**, tanggal selesai **30 Juni**
- **Target:** 10 dokumen
- **Bulan aktif yang dievaluasi:** Mei 2026 (`bf = '2026-05'`)
- **Global stats (contoh):** `c = 50`, `avg_target = 10`

> Karena `bf = '2026-05'`, hanya pengiriman dengan `bulan_pengiriman = '2026-05'` yang masuk perhitungan.

---

### Skenario A — Cicil Tiap Bulan (April 3, Mei 3, Juni 4)

Budi mengirim 3 di April, 3 di Mei, 4 di Juni — semua dengan tipe **Cicilan**.

**Evaluasi bulan Mei:**
- Yang masuk filter: hanya pengiriman bulan Mei → `jumlah_dikirim = 3`, tipe `Cicilan`
- `progress_cicilan = 3`, `progress_pelunasan = 0`
- `b_efektif = 0 + 3 × 0.5 = 1.5`
- `d = max(0, 50 - 10 + 1.5) = 41.5`
- **F1 = (41.5/50) × (1.5/10) × 100 = 83% × 15% × 100 = 12.45**
- F2 = 0 (tidak ada Pelunasan)
- F3 = RR × (3/10) — proporsional
- F4 = Rating × 20 × (3/10)

> Skor rendah karena baru cicilan sebagian. Normal — sistem memang membedakan Cicilan dan Pelunasan.

---

### Skenario B — Kirim Semua di Bulan Terakhir (Juni, tipe Pelunasan)

Budi mengirim semua 10 dokumen di **30 Juni** dengan tipe **Pelunasan**.

**Evaluasi bulan Mei:**
- `bulan_pengiriman` = Juni → **tidak masuk filter bulan Mei**
- `total_penugasan_dikerjakan = 0`
- F1 = F2 = F3 = F4 = 0
- `rata_rata_final = 0`

**Evaluasi bulan Juni:**
- `progress_pelunasan = 10`, `b_efektif = 10`
- `d = max(0, 50 - 10 + 10) = 50`
- **F1 = (50/50) × (10/10) × 100 = 100**
- `lama_rentang = 30 Jun - 1 Apr = 90 hari`
- `lama_pengiriman = 30 Jun - 1 Apr = 90 hari` (tepat deadline)
- **F2 = 80 + ((90-90)/90) × 20 = 80**
- F3 & F4 = penuh (bobot 1.0)

---

### Skenario C — Kirim Semua di Bulan Awal (April, tipe Pelunasan)

Budi mengirim semua 10 dokumen di **5 April** dengan tipe **Pelunasan**.

**Evaluasi bulan April:**
- `progress_pelunasan = 10`, F1 = tinggi
- `lama_pengiriman = 5 Apr - 1 Apr = 4 hari`
- `lama_rentang = 30 Jun - 1 Apr = 90 hari`
- `4 ≤ 90` → tepat waktu bahkan sangat cepat
- **F2 = 80 + ((90-4)/90) × 20 = 80 + 0.9556 × 20 = 99.1** ← skor tinggi!

**Evaluasi bulan Mei:**
- `bulan_pengiriman = April` → **tidak masuk filter bulan Mei**
- Skor Mei = 0

> Sistem hanya mengevaluasi pengiriman di bulan yang dievaluasi. Pengiriman April tidak ikut skor Mei.

---

### Skenario D — Kirim di Luar Range Tanggal Penugasan

Penugasan range: April–Juni. Budi mengirim di **Agustus**.

- `bulan_pengiriman = '2026-08'` → tidak masuk filter manapun dari April–Juni
- Penugasan tetap muncul di `total_penugasan` (karena join penugasans aktif)
- Tapi `lp` (latest_diterima) = NULL → semua F = 0 untuk penugasan ini
- **Penugasan ini tercatat ada, tapi tidak berkontribusi ke skor**

---

### Skenario E — Penugasan Belum Ada Pengiriman Sama Sekali

Budi punya 2 penugasan:
- Penugasan A: sudah ada pengiriman Diterima bulan ini → masuk F1–F4
- Penugasan B: belum ada pengiriman sama sekali → `lp` = NULL

**Yang terjadi:**
- `total_penugasan = 2`
- `total_penugasan_dikerjakan = 1` (hanya penugasan A)
- `target_pegawai = target_A + target_B` (keduanya masuk total target!)
- F1 hanya menghitung progress dari penugasan A, tapi denominator-nya adalah `a` yang sudah mencakup target_B

> **Efek:** Karena target_B ikut di denominator tapi tidak ada progresnya, skor F1 otomatis lebih rendah. Ini adalah perilaku yang disengaja — pegawai yang punya penugasan belum dikerjakan akan terpengaruh skornya, mendorong agar semua penugasan aktif dikerjakan.

---

## 6. 📋 Ringkasan Formula Lengkap

```
b_efektif = progress_pelunasan + progress_cicilan × 0.5
d         = max(0, c - a + b_efektif)

F1 = (d/c) × (b_efektif/a) × 100          [penyelesaian, 0–100]
F2 = avg skor kecepatan Pelunasan           [kecepatan, 70–100]
F3 = Σ(rr × bobot) / n_dikerjakan          [kualitas RR, skala rr]
F4 = Σ(rating × 20 × bobot) / n_dikerjakan [rating, 0–100]

rata_rata_base  = (F1+F2+F3+F4) / 4
koefisien_beban = LEAST(1.15, GREATEST(0.85, a / avg_target))

Jika koefisien ≥ 1.0:
  bonus   = min(base × (koef-1), max(0, 100-base))
  final   = base + bonus

Jika koefisien < 1.0:
  final   = max(0, base × koef)
```

---

## 7. 🔢 Urutan Prioritas Ranking

```
1. Pegawai WITH penugasan aktif di atas pegawai TANPA penugasan
2. rata_rata_final DESC (skor tertinggi duluan)
3. target_pegawai DESC (total target dokumen terbanyak = lebih atas jika skor sama)
4. nama_pegawai ASC (alfabetis jika semua sama)
```

---

## 8. 👑 Mekanisme Ranking Ketua Tim (Katim)

> **Terakhir diperbarui:** Juli 2026  
> **File referensi:** `app/Services/DashboardAnalyticsService.php` (fungsi `rankKetuaTimAll`)  
> **Perubahan utama:** Penilaian Ketua Tim **murni ditentukan dari F5 (Kinerja Pengawasan)** dengan penyesuaian **Koefisien Beban Tim (Fairness)**. Formula F1–F4 individu dinonaktifkan untuk Katim.

Berbeda dengan ranking pegawai biasa, Ketua Tim (Katim) dinilai berdasarkan performa pengawasan terhadap sub-kegiatan yang mereka pimpin beserta seluruh anggota tim di bawahnya.

### 🔬 Formula Penilaian Katim

#### 1. Skor Dasar (Base Score — F5)
Skor dasar Ketua Tim diambil 100% dari nilai **F5 (Kinerja Pengawasan)**:
```
rata_rata_base = F5
```
F5 dihitung dari rata-rata performa seluruh anggota tim di bawah sub-kegiatan yang dipimpin oleh Katim tersebut pada bulan aktif:
- **RR Terima**: Rata-rata response rate pengiriman anggota tim yang sudah berstatus 'Diterima'.
- **Rating Terima**: Rata-rata rating bintang dari pengiriman anggota tim yang sudah berstatus 'Diterima'.

#### 2. Koefisien Beban Kerja Tim (Fairness)
Untuk memberikan keadilan bagi Katim yang memimpin tim dengan beban kerja (volume target) yang besar, diterapkan **Koefisien Beban Tim**:
```
rasio_beban = total_target_tim / avg_target_tim
```
* **total_target_tim**: Jumlah total target penugasan dari seluruh anggota tim di bawah pimpinan Katim tersebut (di bulan evaluasi aktif, baik status 'Diterima' maupun belum).
* **avg_target_tim**: Rata-rata dari total target tim seluruh Katim aktif di kantor pada bulan tersebut.

Koefisien beban tim dibatasi antara **0.85 hingga 1.15**:
```
koefisien_beban = min(1.15, max(0.85, rasio_beban))
```

#### 3. Perhitungan Skor Akhir (Rata-rata Final)
Bonus atau penalti dari koefisien beban diterapkan ke **Rata-rata Base (F5)** untuk menghasilkan skor akhir:

* **Jika koefisien_beban ≥ 1.0 (Bonus Beban Kerja)**
  ```
  bonus_max       = rata_rata_base × (koefisien_beban - 1.0)
  ruang_skor      = max(0.0, 100.0 - rata_rata_base)
  bonus_aktual    = min(bonus_max, ruang_skor)
  rata_rata_final = rata_rata_base + bonus_aktual
  ```

* **Jika koefisien_beban < 1.0 (Penalti Beban Kerja)**
  ```
  bonus_aktual    = rata_rata_base × (koefisien_beban - 1.0) (bernilai negatif)
  rata_rata_final = rata_rata_base + bonus_aktual
  ```

---

### 🔢 Urutan Prioritas Ranking Katim

```
1. Katim dengan penugasan/sub-kegiatan aktif di atas Katim tanpa sub-kegiatan
2. rata_rata_final DESC (skor tertinggi duluan)
3. nama_pegawai ASC (alfabetis jika skor sama)
```
