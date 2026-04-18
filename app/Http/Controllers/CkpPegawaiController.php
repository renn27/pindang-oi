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


    // Export CKP ke Excel format CKP-T
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

    // Konversi tingkat kualitas ke persentase
    $kualitasToPersen = function($nilai) {
        if ($nilai === null) return null;
        return match ((int)$nilai) {
            1 => 20,
            2 => 40,
            3 => 60,
            4 => 80,
            5 => 100,
            default => 0
        };
    };

    if ($bulan !== 'all') {
        $namaBulan = $bulanList[$bulan] ?? '-';
        $hariAkhir = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->day;
        $periodeStr = "1 - {$hariAkhir} {$namaBulan} {$tahun}";
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate   = Carbon::create($tahun, $bulan, 1)->endOfMonth();
        $tanggalPenilaian = "{$hariAkhir} {$namaBulan} {$tahun}";
    } else {
        $namaBulan = 'Semua Bulan';
        $periodeStr = "Tahun {$tahun}";
        $startDate = Carbon::create($tahun, 1, 1)->startOfYear();
        $endDate   = Carbon::create($tahun, 12, 31)->endOfYear();
        $tanggalPenilaian = "31 Desember {$tahun}";
    }

    // Query untuk CKP dari Penugasan (Anggota Tim)
    $ckpFromPenugasan = CkpPegawai::with(['pegawai', 'penugasan.jenisKegiatan', 'penugasan.subKegiatan'])
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
        ->orderBy('jenis_ckp', 'asc')
        ->orderBy('created_at', 'asc')
        ->get();

    $pegawai          = Auth::user();
    $namaPegawai      = $pegawai->nama_pegawai ?? '';
    $jabatan          = $pegawai->jabatan ?? '';
    $satuanOrganisasi = $pegawai->satuan_organisasi ?? 'BPS Kabupaten';
    $nipPegawai       = $pegawai->nip ?? 'NIP Pegawai';
    $namaPejabat      = 'Sukendro Suryo Wiguno,SST, M.Ec.Dev';
    $nipPejabat       = '198211122006021001';

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
    $sheet->getColumnDimension('A')->setWidth(5);   // No
    $sheet->getColumnDimension('B')->setWidth(50);  // Uraian Kegiatan
    $sheet->getColumnDimension('C')->setWidth(12);  // Satuan
    $sheet->getColumnDimension('D')->setWidth(10);  // Target
    $sheet->getColumnDimension('E')->setWidth(12);  // Realisasi
    $sheet->getColumnDimension('F')->setWidth(8);   // %
    $sheet->getColumnDimension('G')->setWidth(14);  // Tingkat Kualitas
    $sheet->getColumnDimension('H')->setWidth(18);  // Kode Butir Kegiatan
    $sheet->getColumnDimension('I')->setWidth(12);  // Angka Kredit
    $sheet->getColumnDimension('J')->setWidth(20);  // Keterangan

    // Konstanta style
    $CENTER   = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $LEFT     = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
    $RIGHT    = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT;
    $MIDDLE   = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $THIN     = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN;
    $MEDIUM   = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM;
    $SOLID    = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID;

    $borderAll = [
        'borders' => [
            'allBorders' => ['borderStyle' => $THIN, 'color' => ['argb' => 'FF000000']],
        ],
    ];

    // ── BARIS 1: Judul dengan CKP-R di kanan atas ────────────
    $sheet->mergeCells('A1:I1');
    $sheet->setCellValue('A1', 'CAPAIAN KINERJA PEGAWAI TAHUN ' . $tahun);
    $sheet->getStyle('A1')->applyFromArray([
        'font'      => ['bold' => true, 'size' => 14, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE],
    ]);
    
    // CKP-R di pojok kanan atas dengan border
    $sheet->setCellValue('J1', 'CKP-R');
    $sheet->getStyle('J1')->applyFromArray([
        'font'      => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE],
        'borders'   => [
            'outline' => ['borderStyle' => $MEDIUM, 'color' => ['argb' => 'FF000000']],
        ],
    ]);
    $sheet->getRowDimension(1)->setRowHeight(25);

    // ── BARIS 2–5: Info pegawai dengan tab yang rapi ─────────
    $infoRows = [
        2 => ['Satuan Organisasi', $satuanOrganisasi],
        3 => ['Nama', $namaPegawai],
        4 => ['Jabatan', $jabatan],
        5 => ['Periode', $periodeStr],
    ];
    
    foreach ($infoRows as $rowNum => $info) {
        $sheet->mergeCells("A{$rowNum}:J{$rowNum}");
        $sheet->setCellValue("A{$rowNum}", $info[0] . ' : ' . $info[1]);
        $sheet->getStyle("A{$rowNum}")->applyFromArray([
            'font'      => ['size' => 11, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $LEFT, 'indent' => 1],
        ]);
        $sheet->getRowDimension($rowNum)->setRowHeight(18);
    }

    // ── BARIS 6: Spasi ───────────────────────────────────────
    $sheet->getRowDimension(6)->setRowHeight(6);

    // ── BARIS 7–9: Header tabel ─────────────────────────────
    $headerRow1 = 7;
    $headerRow2 = 8;
    $headerRow3 = 9;

    // Merge untuk header
    $sheet->mergeCells("A{$headerRow1}:A{$headerRow3}");   // No
    $sheet->mergeCells("B{$headerRow1}:B{$headerRow3}");   // Uraian Kegiatan
    $sheet->mergeCells("C{$headerRow1}:C{$headerRow3}");   // Satuan
    
    // Kuantitas (group header) - D, E, F
    $sheet->mergeCells("D{$headerRow1}:F{$headerRow1}");
    $sheet->setCellValue("D{$headerRow1}", 'Kuantitas');
    
    // Header lainnya
    $sheet->mergeCells("G{$headerRow1}:G{$headerRow3}");   // Tingkat Kualitas
    $sheet->mergeCells("H{$headerRow1}:H{$headerRow3}");   // Kode Butir Kegiatan
    $sheet->mergeCells("I{$headerRow1}:I{$headerRow3}");   // Angka Kredit
    $sheet->mergeCells("J{$headerRow1}:J{$headerRow3}");   // Keterangan

    // Sub-header untuk Kuantitas (baris 8)
    $sheet->setCellValue("D{$headerRow2}", 'Target');
    $sheet->setCellValue("E{$headerRow2}", 'Realisasi');
    $sheet->setCellValue("F{$headerRow2}", '%');

    // Set header utama
    $sheet->setCellValue("A{$headerRow1}", 'No');
    $sheet->setCellValue("B{$headerRow1}", 'Uraian Kegiatan');
    $sheet->setCellValue("C{$headerRow1}", 'Satuan');
    $sheet->setCellValue("G{$headerRow1}", 'Tingkat Kualitas');
    $sheet->setCellValue("H{$headerRow1}", 'Kode Butir Kegiatan');
    $sheet->setCellValue("I{$headerRow1}", 'Angka Kredit');
    $sheet->setCellValue("J{$headerRow1}", 'Keterangan');

    // Style header - WARNA ABU-ABU
    $sheet->getStyle("A{$headerRow1}:J{$headerRow2}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE, 'wrapText' => true],
        'fill'      => ['fillType' => $SOLID, 'startColor' => ['argb' => 'FFD9D9D9']],
    ]);
    $sheet->getRowDimension($headerRow1)->setRowHeight(20);
    $sheet->getRowDimension($headerRow2)->setRowHeight(20);

    // Baris 9: nomor kolom - WARNA ABU-ABU
    $nomorKolom = [
        'A9' => '(1)', 'B9' => '(2)', 'C9' => '(3)', 
        'D9' => '(4)', 'E9' => '(5)', 'F9' => '(6)',
        'G9' => '(7)', 'H9' => '(8)', 'I9' => '(9)', 'J9' => '(10)'
    ];
    foreach ($nomorKolom as $cell => $val) {
        $sheet->setCellValue($cell, $val);
    }
    $sheet->getStyle('A9:J9')->applyFromArray([
        'font'      => ['size' => 9, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE],
        'fill'      => ['fillType' => $SOLID, 'startColor' => ['argb' => 'FFD9D9D9']],
    ]);
    $sheet->getRowDimension(9)->setRowHeight(16);

    $sheet->getStyle("A7:J9")->applyFromArray($borderAll);

    // ─────────────────────────────────────────────────────────
    // HELPER: tulis satu baris data CKP
    // ─────────────────────────────────────────────────────────
    $tulisDataRow = function (int $row, int $no, $ckp) use ($sheet, $borderAll, $CENTER, $LEFT, $MIDDLE, $kualitasToPersen) {
        // Konversi tingkat kualitas ke persen
        $kualitasPersen = $kualitasToPersen($ckp->tingkat_kualitas);
        
        $sheet->setCellValue("A{$row}", $no);
        $sheet->setCellValue("B{$row}", $ckp->uraian ?? '');
        $sheet->setCellValue("C{$row}", $ckp->satuan ?? '');
        $sheet->setCellValue("D{$row}", $ckp->target_kuantitas ?? '0');
        $sheet->setCellValue("E{$row}", $ckp->realisasi ?? '0');
        $sheet->setCellValue("F{$row}", $ckp->persentase_realisasi ? $ckp->persentase_realisasi . '%' : '-');
        $sheet->setCellValue("G{$row}", $kualitasPersen ? $kualitasPersen . '%' : '-');
        $sheet->setCellValue("H{$row}", $ckp->kode_butir_kegiatan ?? '');
        $sheet->setCellValue("I{$row}", $ckp->angka_kredit ?? '');
        $sheet->setCellValue("J{$row}", $ckp->keterangan ?? '');

        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font'      => ['size' => 10, 'name' => 'Arial'],
            'alignment' => ['vertical' => $MIDDLE, 'wrapText' => true],
        ]);
        
        // Alignment per kolom
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($CENTER);
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal($LEFT);
        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal($CENTER);
        $sheet->getStyle("D{$row}:G{$row}")->getAlignment()->setHorizontal($CENTER);
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal($LEFT);
        $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal($CENTER);
        $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal($LEFT);
        
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray($borderAll);
        $sheet->getRowDimension($row)->setRowHeight(-1);
    };

    // HELPER: tulis label seksi (UTAMA / TAMBAHAN) - WARNA ABU-ABU MUDA
    $tulisSeksi = function (int $row, string $label) use ($sheet, $borderAll, $CENTER, $MIDDLE, $SOLID) {
        $sheet->mergeCells("A{$row}:J{$row}");
        $sheet->setCellValue("A{$row}", $label);
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
            'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE],
            'fill'      => ['fillType' => $SOLID, 'startColor' => ['argb' => 'FFE6E6E6']],
        ]);
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray($borderAll);
        $sheet->getRowDimension($row)->setRowHeight(18);
    };

    // ─────────────────────────────────────────────────────────
    $currentRow = 10;

    // ── Seksi UTAMA ──────────────────────────────────────────
    $tulisSeksi($currentRow, 'UTAMA');
    $currentRow++;

    $ckpUtama = $ckpList->where('jenis_ckp', 'utama')->values();
    $noUtama = 1;
    
    if ($ckpUtama->isEmpty()) {
        $sheet->mergeCells("A{$currentRow}:J{$currentRow}");
        $sheet->setCellValue("A{$currentRow}", 'Tidak ada data');
        $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10],
            'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE],
        ]);
        $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray($borderAll);
        $sheet->getRowDimension($currentRow)->setRowHeight(20);
        $currentRow++;
    } else {
        foreach ($ckpUtama as $ckp) {
            $tulisDataRow($currentRow, $noUtama++, $ckp);
            $currentRow++;
        }
    }

    // ── Seksi TAMBAHAN ───────────────────────────────────────
    $tulisSeksi($currentRow, 'TAMBAHAN');
    $currentRow++;

    $ckpTambahan = $ckpList->where('jenis_ckp', 'tambahan')->values();
    $noTambahan = 1;
    
    if ($ckpTambahan->isEmpty()) {
        $sheet->mergeCells("A{$currentRow}:J{$currentRow}");
        $sheet->setCellValue("A{$currentRow}", 'Tidak ada data');
        $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10],
            'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE],
        ]);
        $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray($borderAll);
        $sheet->getRowDimension($currentRow)->setRowHeight(20);
        $currentRow++;
    } else {
        foreach ($ckpTambahan as $ckp) {
            $tulisDataRow($currentRow, $noTambahan++, $ckp);
            $currentRow++;
        }
    }

    // ── Baris JUMLAH - WARNA ABU-ABU ────────────────────────
    $totalTarget = $ckpList->sum('target_kuantitas');
    $totalRealisasi = $ckpList->sum('realisasi');
    $totalAngkaKredit = $ckpList->sum('angka_kredit');
    
    $sheet->mergeCells("A{$currentRow}:C{$currentRow}");
    $sheet->setCellValue("A{$currentRow}", 'JUMLAH');
    $sheet->setCellValue("D{$currentRow}", $totalTarget);
    $sheet->setCellValue("E{$currentRow}", $totalRealisasi);
    $sheet->setCellValue("I{$currentRow}", $totalAngkaKredit ?: 0);
    
    $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE],
        'fill'      => ['fillType' => $SOLID, 'startColor' => ['argb' => 'FFD9D9D9']],
    ]);
    $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray($borderAll);
    $sheet->getRowDimension($currentRow)->setRowHeight(18);
    $currentRow++;

    // ── Baris RATA-RATA - WARNA ABU-ABU MUDA ────────────────
    $countCkp = $ckpList->count();
    $avgPersentase = $countCkp > 0 ? $ckpList->avg('persentase_realisasi') : 0;
    $avgKualitas = $countCkp > 0 ? $ckpList->avg('tingkat_kualitas') : 0;
    $avgKualitasPersen = $kualitasToPersen($avgKualitas);
    
    $sheet->mergeCells("A{$currentRow}:C{$currentRow}");
    $sheet->setCellValue("A{$currentRow}", 'RATA-RATA');
    $sheet->setCellValue("F{$currentRow}", $avgPersentase ? round($avgPersentase, 2) . '%' : '-');
    $sheet->setCellValue("G{$currentRow}", $avgKualitasPersen ? $avgKualitasPersen . '%' : '-');
    
    $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE],
        'fill'      => ['fillType' => $SOLID, 'startColor' => ['argb' => 'FFE6E6E6']],
    ]);
    $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray($borderAll);
    $sheet->getRowDimension($currentRow)->setRowHeight(18);
    $currentRow++;

    // ── Baris CAPAIAN KINERJA PEGAWAI (CKP) - WARNA HIJAU ───
    // Merge hanya 2 kolom: F (%), G (Tingkat Kualitas)
    $ckpFinal = ($avgPersentase + $avgKualitasPersen) / 2;
    
    $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
    $sheet->setCellValue("A{$currentRow}", 'CAPAIAN KINERJA PEGAWAI (CKP)');
    $sheet->mergeCells("F{$currentRow}:G{$currentRow}");
    $sheet->setCellValue("F{$currentRow}", round($ckpFinal, 3) . '%');
    
    $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER, 'vertical' => $MIDDLE],
        'fill'      => ['fillType' => $SOLID, 'startColor' => ['argb' => 'FFB8D9B8']],
    ]);
    $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray($borderAll);
    $sheet->getRowDimension($currentRow)->setRowHeight(22);
    $currentRow += 2;

    // ── Penilaian Kinerja (Posisi Kiri) ─────────────────────
    $sheet->mergeCells("A{$currentRow}:C{$currentRow}");
    $sheet->setCellValue("A{$currentRow}", 'Penilaian Kinerja');
    $sheet->getStyle("A{$currentRow}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $LEFT],
    ]);
    $sheet->getRowDimension($currentRow)->setRowHeight(18);
    $currentRow++;

    $sheet->mergeCells("A{$currentRow}:C{$currentRow}");
    $sheet->setCellValue("A{$currentRow}", 'Tanggal : ' . $tanggalPenilaian);
    $sheet->getStyle("A{$currentRow}")->applyFromArray([
        'font'      => ['size' => 11, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $LEFT],
    ]);
    $sheet->getRowDimension($currentRow)->setRowHeight(18);
    $currentRow += 2;

    // ── Tanda Tangan ─────────────────────────────────────────
    $ttdRow = $currentRow;

    // Label
    $sheet->mergeCells("A{$ttdRow}:E{$ttdRow}");
    $sheet->setCellValue("A{$ttdRow}", 'Pegawai Yang Dinilai');
    $sheet->getStyle("A{$ttdRow}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER],
    ]);

    $sheet->mergeCells("F{$ttdRow}:J{$ttdRow}");
    $sheet->setCellValue("F{$ttdRow}", 'Pejabat Penilai');
    $sheet->getStyle("F{$ttdRow}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER],
    ]);
    $sheet->getRowDimension($ttdRow)->setRowHeight(18);

    // 4 baris ruang tanda tangan
    for ($i = 1; $i <= 4; $i++) {
        $sheet->getRowDimension($ttdRow + $i)->setRowHeight(18);
    }

    $namaRow = $ttdRow + 5;
    $nipRow  = $ttdRow + 6;

    $sheet->mergeCells("A{$namaRow}:E{$namaRow}");
    $sheet->setCellValue("A{$namaRow}", '( ' . $namaPegawai . ' )');
    $sheet->getStyle("A{$namaRow}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER],
    ]);

    $sheet->mergeCells("F{$namaRow}:J{$namaRow}");
    $sheet->setCellValue("F{$namaRow}", $namaPejabat ? '( ' . $namaPejabat . ' )' : '');
    $sheet->getStyle("F{$namaRow}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER],
    ]);
    $sheet->getRowDimension($namaRow)->setRowHeight(18);

    $sheet->mergeCells("A{$nipRow}:E{$nipRow}");
    $sheet->setCellValue("A{$nipRow}", $nipPegawai ? 'NIP. ' . $nipPegawai : '');
    $sheet->getStyle("A{$nipRow}")->applyFromArray([
        'font'      => ['size' => 10, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER],
    ]);

    $sheet->mergeCells("F{$nipRow}:J{$nipRow}");
    $sheet->setCellValue("F{$nipRow}", $nipPejabat ? 'NIP. ' . $nipPejabat : '');
    $sheet->getStyle("F{$nipRow}")->applyFromArray([
        'font'      => ['size' => 10, 'name' => 'Arial'],
        'alignment' => ['horizontal' => $CENTER],
    ]);
    $sheet->getRowDimension($nipRow)->setRowHeight(18);

    // ── Output ───────────────────────────────────────────────
    $namaFile = 'CKP-T_' . ($bulan === 'all' ? 'Semua_Bulan' : $namaBulan) . '_' . $tahun . '.xlsx';

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
