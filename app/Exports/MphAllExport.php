<?php

namespace App\Exports;

use App\Models\Bidang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithEvents
};
use Maatwebsite\Excel\Events\AfterSheet;

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
                'kegiatans.penanggungJawab' // pastikan relasi ketua
            ]);

            foreach ($bidang->kegiatans as $kegiatan) {
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
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Merge kolom Bidang (A)
                $this->mergeColumn($sheet, 'A', 2, $highestRow);

                // Merge kolom Kegiatan (B)
                $this->mergeColumn($sheet, 'B', 2, $highestRow);

                // Merge kolom Nama Ketua (C)
                $this->mergeColumn($sheet, 'C', 2, $highestRow);

                // Merge kolom Sub Kegiatan (D)
                $this->mergeColumn($sheet, 'D', 2, $highestRow);

                // Set border all
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }

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
                        ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
                $lastValue = $value;
                $mergeStart = $row;
            }
        }

        // Merge terakhir
        if ($endRow > $mergeStart) {
            $sheet->mergeCells("{$col}{$mergeStart}:{$col}{$endRow}");
            $sheet->getStyle("{$col}{$mergeStart}")
                ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }
    }
}  