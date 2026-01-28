<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithEvents
};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MphAllExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $bidangs;

    public function __construct($bidangs)
    {
        $this->bidangs = $bidangs;
    }

    public function collection(): Collection
    {
        $data = collect();

        foreach ($this->bidangs as $bidang) {
            $bidang->load([
                'kegiatans.subKegiatans.penugasans.anggota',
                'kegiatans.subKegiatans.penugasans.jenisKegiatan',
                'kegiatans.penanggungJawab'
            ]);

            foreach ($bidang->kegiatans as $kegiatan) {
                // 1 kegiatan = 1 ketua
                $namaKetua = $kegiatan->penanggungJawab->nama_pegawai ?? '-';

                foreach ($kegiatan->subKegiatans as $sub) {
                    foreach ($sub->penugasans as $p) {
                        $data->push([
                            'bidang'         => $bidang->nama_bidang,
                            'kegiatan'       => $kegiatan->nama_rk_kegiatan,
                            'nama_ketua'     => $namaKetua,
                            'sub_kegiatan'   => $sub->nama_sub_kegiatan,
                            'nama_pegawai'   => $p->anggota->nama_pegawai ?? '-',
                            'jenis_kegiatan' => $p->jenisKegiatan->jenis_kegiatan ?? '-',
                            'target'         => $p->target,
                            'satuan'         => $p->satuan_target,
                        ]);
                    }
                }
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Bidang',
            'Kegiatan',
            'Nama Ketua',
            'Sub Kegiatan',
            'Nama Pegawai',
            'Jenis Kegiatan',
            'Target',
            'Satuan',
        ];
    }

    public function map($row): array
    {
        return [
            $row['bidang'],
            $row['kegiatan'],
            $row['nama_ketua'],
            $row['sub_kegiatan'],
            $row['nama_pegawai'],
            $row['jenis_kegiatan'],
            $row['target'],
            $row['satuan'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Merge Bidang
                $this->mergeColumn($sheet, 'A', 2, $highestRow);

                // 🔥 Merge Kegiatan + Ketua (IKUT KEGIATAN)
                $this->mergeKegiatanDanKetua($sheet, 2, $highestRow);

                // Merge Sub Kegiatan
                $this->mergeColumn($sheet, 'D', 2, $highestRow);

                // Border semua cell
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }

    /**
     * Merge kolom standar (berdasarkan nilai sel)
     */
    private function mergeColumn($sheet, $col, $startRow, $endRow)
    {
        $lastValue = null;
        $mergeStart = $startRow;

        for ($row = $startRow; $row <= $endRow; $row++) {
            $value = $sheet->getCell("{$col}{$row}")->getValue();

            if ($value !== $lastValue) {
                if ($row - 1 > $mergeStart) {
                    $sheet->mergeCells("{$col}{$mergeStart}:{$col}" . ($row - 1));
                    $sheet->getStyle("{$col}{$mergeStart}")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }

                $lastValue = $value;
                $mergeStart = $row;
            }
        }

        if ($endRow > $mergeStart) {
            $sheet->mergeCells("{$col}{$mergeStart}:{$col}{$endRow}");
            $sheet->getStyle("{$col}{$mergeStart}")
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
    }

    /**
     * 🔥 LOGIKA FINAL
     * Nama Ketua (C) SELALU ikut merge Kegiatan (B)
     * Karena: 1 kegiatan = 1 ketua
     */
    private function mergeKegiatanDanKetua($sheet, $startRow, $endRow)
    {
        $lastKegiatan = null;
        $mergeStart = $startRow;

        for ($row = $startRow; $row <= $endRow; $row++) {
            $kegiatan = $sheet->getCell("B{$row}")->getValue();

            if ($kegiatan !== $lastKegiatan) {
                if ($row - 1 > $mergeStart) {
                    // Merge Kegiatan
                    $sheet->mergeCells("B{$mergeStart}:B" . ($row - 1));
                    $sheet->getStyle("B{$mergeStart}")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    // Merge Ketua IKUT kegiatan
                    $sheet->mergeCells("C{$mergeStart}:C" . ($row - 1));
                    $sheet->getStyle("C{$mergeStart}")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }

                $mergeStart = $row;
                $lastKegiatan = $kegiatan;
            }
        }

        // Merge terakhir
        if ($endRow > $mergeStart) {
            $sheet->mergeCells("B{$mergeStart}:B{$endRow}");
            $sheet->getStyle("B{$mergeStart}")
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->mergeCells("C{$mergeStart}:C{$endRow}");
            $sheet->getStyle("C{$mergeStart}")
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
    }
}
