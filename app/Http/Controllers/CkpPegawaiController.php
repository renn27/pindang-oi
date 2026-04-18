<?php

namespace App\Http\Controllers;

use App\Models\Penugasan;
use App\Models\CkpPegawai;
use App\Models\SubKegiatan; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CkpPegawaiController extends Controller
{
    public function index(Request $request)
{
    $bulan = $request->get('bulan', 'all');
    $tahun = $request->get('tahun', date('Y'));
    $userId = Auth::user()->id_pegawai;

    if ($bulan !== 'all') {
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate   = Carbon::create($tahun, $bulan, 1)->endOfMonth();
    } else {
        $startDate = Carbon::create($tahun, 1, 1)->startOfYear();
        $endDate   = Carbon::create($tahun, 12, 31)->endOfYear();
    }

    // Query untuk CKP dari Penugasan (Anggota Tim)
    $ckpFromPenugasan = CkpPegawai::with(['pegawai', 'penugasan.jenisKegiatan', 'penugasan.subKegiatan', 'penugasan.pengirimans', 'penugasan.latestPengiriman'])
        ->where('id_pegawai', $userId)
        ->whereNotNull('id_penugasan')
        ->whereHas('penugasan.pengirimans', function ($query) use ($startDate, $endDate) {
            $query->whereDate('tanggal_pengiriman', '<=', $endDate)
                    ->whereDate('tanggal_pengiriman', '>=', $startDate);
        });

    // Query untuk CKP dari Sub Kegiatan (Ketua Tim)
    $ckpFromSubKegiatan = CkpPegawai::with(['pegawai', 'subKegiatan.kegiatan'])
        ->where('id_pegawai', $userId)
        ->whereNotNull('id_sub_kegiatan')
        ->whereNull('id_penugasan')
        ->whereDate('created_at', '<=', $endDate)
        ->whereDate('created_at', '>=', $startDate);

    // Gabungkan kedua query
    $ckpList = $ckpFromPenugasan->union($ckpFromSubKegiatan)
        ->orderBy('created_at', 'desc')
        ->get();

    $totalTarget = $ckpList->sum('target_kuantitas');
    $totalCkp = $ckpList->count();

    $bulanList = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];

    // Tahun list dari gabungan kedua sumber
    $tahunPenugasan = CkpPegawai::join('penugasans', 'penugasans.id_penugasan', '=', 'ckp_pegawais.id_penugasan')
        ->join('pengirimans', 'pengirimans.id_penugasan', '=', 'penugasans.id_penugasan')
        ->where('ckp_pegawais.id_pegawai', $userId)
        ->select(DB::raw('DISTINCT YEAR(pengirimans.tanggal_pengiriman) as tahun'));

    $tahunSubKegiatan = CkpPegawai::where('id_pegawai', $userId)
        ->whereNotNull('id_sub_kegiatan')
        ->whereNull('id_penugasan')
        ->select(DB::raw('DISTINCT YEAR(created_at) as tahun'));

    $tahunList = $tahunPenugasan->union($tahunSubKegiatan)
        ->orderBy('tahun', 'desc')
        ->pluck('tahun')
        ->toArray();

    if (empty($tahunList)) {
        $tahunList = [date('Y')];
    }

    $title = 'CKP Saya';

    return view('pages.main.pegawai.tagihan-kerja.ckp-pegawai', compact(
        'title',
        'ckpList',
        'totalTarget',
        'totalCkp',
        'bulan',
        'tahun',
        'bulanList',
        'tahunList'
    ));
}

    public function storeFromPenugasan(Request $request, $id)
    {
        $request->validate([
            'uraian' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $penugasan = Penugasan::with([
            'jenisKegiatan',
            'subKegiatan',
            'ckp',
            'latestPengiriman'
        ])->findOrFail($id);

        if ($penugasan->ckp) {
            return redirect()->back()->with('warning', 'Penugasan ini sudah masuk CKP');
        }

        if (!$penugasan->latestPengiriman) {
            return back()->with('error', 'Belum ada pengiriman');
        }

        $latestPengiriman = $penugasan->latestPengiriman;

        $realisasi = $latestPengiriman?->jumlah_dikirim ?? 0;
        $persentase = $latestPengiriman?->rr_kirim ?? 0;
        $kualitas = $latestPengiriman?->rating_kirim ?? 0;

        CkpPegawai::create([
            'id_pegawai'        => $penugasan->id_anggota,
            'id_penugasan'      => $penugasan->id_penugasan,
            'uraian'            => $request->uraian,
            'jenis_ckp'         => 'utama',
            'satuan'            => $penugasan->satuan_target,
            'target_kuantitas'  => $penugasan->target,
            'kode_butir_kegiatan' => null,
            'angka_kredit'      => null,
            'keterangan'        => $request->keterangan,
            'realisasi' => $realisasi,
            'persentase_realisasi' => $persentase,
            'tingkat_kualitas' => $kualitas,
        ]);

        return redirect()->back()
            ->with('success', 'Berhasil dijadikan CKP');
    }

    /**
 * Store CKP dari Sub Kegiatan (untuk Ketua Tim)
 */
public function storeFromSubKegiatan(Request $request, SubKegiatan $subKegiatan)
{
    // 1. Validasi input
    $request->validate([
        'uraian' => 'required|string',
        'keterangan' => 'nullable|string',
    ]);

    // 2. Ambil data kegiatan dan id ketua tim
    $kegiatan = $subKegiatan->kegiatan;
    $idKetuaTim = $kegiatan->id_penanggung_jawab;

    // 3. Pastikan yang akses adalah Ketua Tim
    if (Auth::user()->id_pegawai !== $idKetuaTim) {
        return back()->with('error', 'Hanya Ketua Tim yang dapat membuat CKP Ketua Tim.');
    }

    // 4. Cek apakah semua penugasan sudah selesai (sudah CKP)
    $totalPenugasan = $subKegiatan->penugasans()->count();
    $penugasanSelesai = $subKegiatan->penugasans()
        ->whereHas('ckp')
        ->count();

    if ($totalPenugasan === 0) {
        return back()->with('error', 'Sub kegiatan belum memiliki penugasan.');
    }

    if ($penugasanSelesai < $totalPenugasan) {
        return back()->with('error', 'Semua penugasan harus selesai (CKP) terlebih dahulu.');
    }

    // 5. Cek apakah Ketua Tim sudah memiliki CKP untuk sub kegiatan ini
    $existingCkp = CkpPegawai::where('id_sub_kegiatan', $subKegiatan->id_sub_kegiatan)
        ->where('id_pegawai', $idKetuaTim)
        ->first();

    if ($existingCkp) {
        return back()->with('error', 'Sub kegiatan ini sudah memiliki CKP Ketua Tim.');
    }

    // 6. Hitung total realisasi dari semua penugasan
    $totalRealisasi = $subKegiatan->penugasans()
        ->with('latestPengiriman')
        ->get()
        ->sum(function ($penugasan) {
            return $penugasan->latestPengiriman?->jumlah_dikirim ?? 0;
        });

    // 7. Hitung rata-rata persentase realisasi
    $avgPersentase = $subKegiatan->penugasans()
        ->with('latestPengiriman')
        ->get()
        ->avg(function ($penugasan) {
            return $penugasan->latestPengiriman?->rr_kirim ?? 0;
        }) ?? 0;

    // 8. Hitung rata-rata tingkat kualitas
    $avgKualitas = $subKegiatan->penugasans()
        ->with('latestPengiriman')
        ->get()
        ->avg(function ($penugasan) {
            return $penugasan->latestPengiriman?->rating_kirim ?? 0;
        }) ?? 0;

    // 9. Buat CKP untuk Ketua Tim
    CkpPegawai::create([
        'id_pegawai' => $idKetuaTim,
        'id_sub_kegiatan' => $subKegiatan->id_sub_kegiatan,  // Terisi
        'id_penugasan' => null,                               // NULL
        'uraian' => $request->uraian,
        'jenis_ckp' => 'utama',
        'satuan' => 'Kegiatan',
        'target_kuantitas' => 1,
        'kode_butir_kegiatan' => null,
        'angka_kredit' => null,
        'keterangan' => $request->keterangan,
        'realisasi' => $totalRealisasi,
        'persentase_realisasi' => round($avgPersentase, 2),
        'tingkat_kualitas' => round($avgKualitas, 2),
    ]);

    // 10. Redirect dengan pesan sukses
    return redirect()->back()
        ->with('success', 'CKP Ketua Tim berhasil dibuat.');
}

    /**
     * Update CKP
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'uraian' => 'required|string',
            'jenis_ckp' => 'required|in:utama,tambahan',
            'keterangan' => 'nullable|string',
        ]);

        $ckp = CkpPegawai::findOrFail($id);

        // Pastikan hanya pemilik data yang bisa edit
        if ($ckp->id_pegawai !== Auth::user()->id_pegawai) {
            return redirect()->route('ckp.pegawai.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit data ini');
        }

        $ckp->update([
            'uraian' => $request->uraian,
            'jenis_ckp' => $request->jenis_ckp,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('ckp.pegawai.index')
            ->with('success', 'CKP berhasil diperbarui');
    }


    //  Export CKP ke Excel format CKP-T
    public function exportExcel(Request $request)
    {
        $bulan = $request->get('bulan', 'all');
        $tahun = $request->get('tahun', date('Y'));
        $userId = Auth::user()->id_pegawai;

        $bulanList = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        if ($bulan !== 'all') {
            $namaBulan = $bulanList[$bulan] ?? '-';
            $hariAkhir = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->day;
            $periodeStr = "1 - {$hariAkhir} {$namaBulan} {$tahun}";
        } else {
            $namaBulan = 'Semua Bulan';
            $periodeStr = "Tahun {$tahun}";
        }

        if ($bulan !== 'all') {
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $endDate   = Carbon::create($tahun, $bulan, 1)->endOfMonth();
        } else {
            $startDate = Carbon::create($tahun, 1, 1)->startOfYear();
            $endDate   = Carbon::create($tahun, 12, 31)->endOfYear();
        }

        $ckpList = CkpPegawai::with(['pegawai', 'penugasan'])
            ->where('id_pegawai', $userId)
            ->whereHas('penugasan.pengirimans', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_pengiriman', [$startDate, $endDate]);
            })
            ->orderBy('jenis_ckp', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $pegawai          = Auth::user();
        $namaPegawai      = $pegawai->nama_pegawai ?? '';
        $jabatan          = $pegawai->jabatan ?? '';
        $satuanOrganisasi = $pegawai->satuan_organisasi ?? 'BPS Kabupaten';
        $nipPegawai       = $pegawai->nip ?? 'NIP Pegawai';
        $namaPejabat      = 'Nama Pejabat Penilai';
        $nipPejabat       = 'NIP Pejabat Penilai';

        // ─────────────────────────────────────────────────────────
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CKP-T');

        // Page setup
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
            ->setFitToPage(true)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.75)->setBottom(0.75)->setLeft(0.7)->setRight(0.7);

        // Lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(55);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(13);
        $sheet->getColumnDimension('G')->setWidth(20);

        // Konstanta style
        $CENTER   = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
        $LEFT     = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
        $MIDDLE   = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
        $THIN     = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN;
        $SOLID    = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID;

        $borderAll = [
            'borders' => [
                'allBorders' => ['borderStyle' => $THIN, 'color' => ['argb' => 'FF000000']],
            ],
        ];

        // ── BARIS 1: Judul ───────────────────────────────────────
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'CKP-T');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // ── BARIS 2: Sub judul ───────────────────────────────────
        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'CAPAIAN KINERJA PEGAWAI TAHUN ' . $tahun);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // ── BARIS 3–6: Info pegawai ──────────────────────────────
        $infoRows = [
            3 => 'Satuan Organisasi : ' . $satuanOrganisasi,
            4 => 'Nama              : ' . $namaPegawai,
            5 => 'Jabatan           : ' . $jabatan,
            6 => 'Periode           : ' . $periodeStr,
        ];
        foreach ($infoRows as $rowNum => $value) {
            $sheet->mergeCells("A{$rowNum}:G{$rowNum}");
            $sheet->setCellValue("A{$rowNum}", $value);
            $sheet->getStyle("A{$rowNum}")->applyFromArray([
                'font'      => ['size' => 10, 'name' => 'Arial'],
                'alignment' => ['horizontal' => $LEFT, 'indent' => 1],
            ]);
            $sheet->getRowDimension($rowNum)->setRowHeight(15);
        }

        // ── BARIS 7: Spasi ───────────────────────────────────────
        $sheet->getRowDimension(7)->setRowHeight(6);

        // ── BARIS 8–10: Header tabel ─────────────────────────────
        // Merge baris 8 & 9 untuk tiap kolom
        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet->mergeCells("{$col}8:{$col}9");
        }

        $headers = [
            'A8' => 'No',
            'B8' => 'Uraian Kegiatan',
            'C8' => 'Satuan',
            'D8' => 'Target Kuantitas',
            'E8' => 'Kode Butir Kegiatan',
            'F8' => 'Angka Kredit',
            'G8' => 'Keterangan',
        ];
        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $sheet->getStyle('A8:G9')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE, 'wrapText' => true],
            'fill'      => ['fillType' => $SOLID, 'startColor' => ['argb' => 'FFD9D9D9']],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(20);
        $sheet->getRowDimension(9)->setRowHeight(20);

        // Baris 10: nomor kolom (1)(2)...(7)
        $nomorKolom = ['A10' => '(1)', 'B10' => '(2)', 'C10' => '(3)', 'D10' => '(4)', 'E10' => '(5)', 'F10' => '(6)', 'G10' => '(7)'];
        foreach ($nomorKolom as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }
        $sheet->getStyle('A10:G10')->applyFromArray([
            'font'      => ['size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE],
            'fill'      => ['fillType' => $SOLID, 'startColor' => ['argb' => 'FFD9D9D9']],
        ]);
        $sheet->getRowDimension(10)->setRowHeight(15);

        $sheet->getStyle('A8:G10')->applyFromArray($borderAll);

        // ─────────────────────────────────────────────────────────
        // HELPER: tulis satu baris data CKP
        // ─────────────────────────────────────────────────────────
        $tulisDataRow = function (int $row, int $no, $ckp) use ($sheet, $borderAll, $CENTER, $MIDDLE) {
            $sheet->setCellValue("A{$row}", $no);
            $sheet->setCellValue("B{$row}", $ckp->uraian ?? '');
            $sheet->setCellValue("C{$row}", $ckp->satuan ?? '');
            $sheet->setCellValue("D{$row}", $ckp->target_kuantitas ?? '');
            $sheet->setCellValue("E{$row}", $ckp->kode_butir_kegiatan ?? '');
            $sheet->setCellValue("F{$row}", $ckp->angka_kredit ?? '');
            $sheet->setCellValue("G{$row}", $ckp->keterangan ?? '');

            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                'font'      => ['size' => 10, 'name' => 'Arial'],
                'alignment' => ['vertical' => $MIDDLE, 'wrapText' => true],
            ]);
            foreach (['A', 'C', 'D', 'F'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal($CENTER);
            }
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($borderAll);
            $sheet->getRowDimension($row)->setRowHeight(-1); // auto height
        };

        // HELPER: tulis label seksi (UTAMA / TAMBAHAN)
        $tulisSeksi = function (int $row, string $label) use ($sheet, $borderAll, $CENTER, $MIDDLE, $SOLID) {
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->setCellValue("A{$row}", $label);
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE],
                'fill'      => ['fillType' => $SOLID, 'startColor' => ['argb' => 'FFEFEFEF']],
            ]);
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($borderAll);
            $sheet->getRowDimension($row)->setRowHeight(16);
        };

        // HELPER: baris kosong jika tidak ada data
        $tulisKosong = function (int $row) use ($sheet, $borderAll) {
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($borderAll);
            $sheet->getRowDimension($row)->setRowHeight(16);
        };

        // ─────────────────────────────────────────────────────────
        $currentRow = 11;

        // ── Seksi UTAMA ──────────────────────────────────────────
        $tulisSeksi($currentRow, 'UTAMA');
        $currentRow++;

        $ckpUtama = $ckpList->where('jenis_ckp', 'utama')->values();
        if ($ckpUtama->isEmpty()) {
            $tulisKosong($currentRow);
            $currentRow++;
        } else {
            foreach ($ckpUtama as $i => $ckp) {
                $tulisDataRow($currentRow, $i + 1, $ckp);
                $currentRow++;
            }
        }

        // ── Seksi TAMBAHAN ───────────────────────────────────────
        $tulisSeksi($currentRow, 'TAMBAHAN');
        $currentRow++;

        $ckpTambahan = $ckpList->where('jenis_ckp', 'tambahan')->values();
        if ($ckpTambahan->isEmpty()) {
            $tulisKosong($currentRow);
            $currentRow++;
        } else {
            foreach ($ckpTambahan as $i => $ckp) {
                $tulisDataRow($currentRow, $i + 1, $ckp);
                $currentRow++;
            }
        }

        // ── Baris JUMLAH ─────────────────────────────────────────
        $sheet->mergeCells("A{$currentRow}:C{$currentRow}");
        $sheet->setCellValue("A{$currentRow}", 'JUMLAH');
        $sheet->setCellValue("D{$currentRow}", $ckpList->sum('target_kuantitas'));
        $sheet->setCellValue("F{$currentRow}", $ckpList->sum('angka_kredit') ?: 0);
        $sheet->getStyle("A{$currentRow}:G{$currentRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE],
            'fill'      => ['fillType' => $SOLID, 'startColor' => ['argb' => 'FFD9D9D9']],
        ]);
        $sheet->getStyle("A{$currentRow}:G{$currentRow}")->applyFromArray($borderAll);
        $sheet->getRowDimension($currentRow)->setRowHeight(16);
        $currentRow += 2;

        // ── Kesepakatan Target ───────────────────────────────────
        $sheet->mergeCells("A{$currentRow}:G{$currentRow}");
        $sheet->setCellValue("A{$currentRow}", 'Kesepakatan Target');
        $sheet->getStyle("A{$currentRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER],
        ]);
        $sheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;

        $sheet->mergeCells("A{$currentRow}:G{$currentRow}");
        $sheet->setCellValue("A{$currentRow}", 'Tanggal : 01 ' . $namaBulan . ' ' . $tahun);
        $sheet->getStyle("A{$currentRow}")->applyFromArray([
            'font'      => ['size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER],
        ]);
        $sheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow += 2; // +1 spasi sebelum tanda tangan

        // ── Tanda Tangan ─────────────────────────────────────────
        $ttdRow = $currentRow;

        // Label
        $sheet->mergeCells("A{$ttdRow}:C{$ttdRow}");
        $sheet->setCellValue("A{$ttdRow}", 'Pegawai Yang Dinilai');
        $sheet->getStyle("A{$ttdRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER],
        ]);

        $sheet->mergeCells("E{$ttdRow}:G{$ttdRow}");
        $sheet->setCellValue("E{$ttdRow}", 'Pejabat Penilai');
        $sheet->getStyle("E{$ttdRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER],
        ]);
        $sheet->getRowDimension($ttdRow)->setRowHeight(15);

        // 4 baris ruang tanda tangan
        for ($i = 1; $i <= 4; $i++) {
            $sheet->getRowDimension($ttdRow + $i)->setRowHeight(15);
        }

        // Nama & NIP
        $namaRow = $ttdRow + 5;
        $nipRow  = $ttdRow + 6;

        $sheet->mergeCells("A{$namaRow}:C{$namaRow}");
        $sheet->setCellValue("A{$namaRow}", '( ' . $namaPegawai . ' )');
        $sheet->getStyle("A{$namaRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER],
        ]);
        $sheet->getRowDimension($namaRow)->setRowHeight(15);

        $sheet->mergeCells("E{$namaRow}:G{$namaRow}");
        $sheet->setCellValue("E{$namaRow}", $namaPejabat ? '( ' . $namaPejabat . ' )' : '');
        $sheet->getStyle("E{$namaRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER],
        ]);

        $sheet->mergeCells("A{$nipRow}:C{$nipRow}");
        $sheet->setCellValue("A{$nipRow}", $nipPegawai ? 'NIP. ' . $nipPegawai : '');
        $sheet->getStyle("A{$nipRow}")->applyFromArray([
            'font'      => ['size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER],
        ]);
        $sheet->getRowDimension($nipRow)->setRowHeight(15);

        $sheet->mergeCells("E{$nipRow}:G{$nipRow}");
        $sheet->setCellValue("E{$nipRow}", $nipPejabat ? 'NIP. ' . $nipPejabat : '');
        $sheet->getStyle("E{$nipRow}")->applyFromArray([
            'font'      => ['size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER],
        ]);
        $sheet->getRowDimension($nipRow)->setRowHeight(15);

        // ── Output ───────────────────────────────────────────────
        $namaFile = 'CKP-T_' . $namaBulan . '_' . $tahun . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $namaFile . '"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
