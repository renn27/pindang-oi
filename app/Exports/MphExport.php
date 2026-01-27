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

class MphExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $bidang;
    protected $pegawai;

    public function __construct($bidang, $pegawai)
    {
        $this->bidang  = $bidang;
        $this->pegawai = $pegawai;
    }

    public function collection(): Collection
    {
        $query = $this->bidang->kegiatans()
            ->with(['subKegiatans.penugasans.anggota', 'subKegiatans.penugasans.jenisKegiatan']);

        if ($this->pegawai->active_role === 'Anggota Tim') {
            $query->forAnggota($this->pegawai);
        }

        if ($this->pegawai->active_role === 'Ketua Tim') {
            $query->forKetua($this->pegawai);
        }

        $data = collect();

        foreach ($query->get() as $kegiatan) {
            foreach ($kegiatan->subKegiatans as $sub) {
                foreach ($sub->penugasans as $p) {
                    $data->push([
                        'kegiatan'       => $kegiatan->nama_rk_kegiatan,
                        'sub_kegiatan'   => $sub->nama_sub_kegiatan,
                        'nama_pegawai'   => $p->anggota->nama_pegawai,
                        'jenis_kegiatan' => $p->jenisKegiatan->jenis_kegiatan,
                        'target'         => $p->target,
                        'satuan'         => $p->satuan_target,
                    ]);
                }
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Kegiatan',
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
            $row['kegiatan'],
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

                // Merge Kegiatan & Sub Kegiatan jika sama berturut-turut
                $this->mergeColumn($sheet, 'A', 2, $highestRow); // Kegiatan
                $this->mergeColumn($sheet, 'B', 2, $highestRow); // Sub Kegiatan

                // Set border all
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
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
                    $sheet->getStyle("{$col}{$mergeStart}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
                $lastValue = $value;
                $mergeStart = $row;
            }
        }

        // Merge terakhir
        if ($endRow > $mergeStart) {
            $sheet->mergeCells("{$col}{$mergeStart}:{$col}{$endRow}");
            $sheet->getStyle("{$col}{$mergeStart}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }
    }
}
