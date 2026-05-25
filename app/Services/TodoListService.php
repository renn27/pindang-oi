<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\Penugasan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TodoListService
{
    public function unfinishedAsAnggota(Pegawai $pegawai): Builder
    {
        return Penugasan::with($this->todoRelations())
            ->where('id_anggota', $pegawai->id_pegawai)
            ->whereDoesntHave('pengirimans.penerimaan', function ($query) {
                $query->where('status', 'Diterima');
            });
    }

    public function berjalanAsAnggota(Pegawai $pegawai): Builder
    {
        return $this->unfinishedAsAnggota($pegawai)
            ->where('tanggal_selesai', '>=', now()->format('Y-m-d'))
            ->orderBy('tanggal_selesai', 'asc');
    }

    public function terlewatAsAnggota(Pegawai $pegawai): Builder
    {
        return $this->unfinishedAsAnggota($pegawai)
            ->where('tanggal_selesai', '<', now()->format('Y-m-d'))
            ->orderBy('tanggal_selesai', 'asc');
    }

    public function revisiAsAnggota(Pegawai $pegawai): Collection
    {
        return Penugasan::with([
            ...$this->todoRelations(),
            'latestPengiriman.penerimaan',
        ])
            ->where('id_anggota', $pegawai->id_pegawai)
            ->whereHas('pengirimans.penerimaan', function ($query) {
                $query->where('status', 'Revisi');
            })
            ->whereDoesntHave('pengirimans.penerimaan', function ($query) {
                $query->where('status', 'Diterima');
            })
            ->get()
            ->filter(function (Penugasan $penugasan) {
                $latest = $penugasan->latestPengiriman;

                return $latest && $latest->penerimaan && $latest->penerimaan->status === 'Revisi';
            });
    }

    public function unfinishedAsKetua(Pegawai $pegawai): Builder
    {
        return Penugasan::with($this->todoRelations())
            ->whereHas('subKegiatan.kegiatan', function ($query) use ($pegawai) {
                $query->where('id_penanggung_jawab', $pegawai->id_pegawai);
            })
            ->whereDoesntHave('pengirimans.penerimaan', function ($query) {
                $query->where('status', 'Diterima');
            });
    }

    public function berjalanAsKetua(Pegawai $pegawai): Builder
    {
        return $this->unfinishedAsKetua($pegawai)
            ->where('tanggal_selesai', '>=', now()->format('Y-m-d'))
            ->orderBy('tanggal_selesai', 'asc');
    }

    public function terlewatAsKetua(Pegawai $pegawai): Builder
    {
        return $this->unfinishedAsKetua($pegawai)
            ->where('tanggal_selesai', '<', now()->format('Y-m-d'))
            ->orderBy('tanggal_selesai', 'asc');
    }

    public function revisiAsKetua(Pegawai $pegawai): Collection
    {
        return Penugasan::with($this->todoRelations())
            ->whereHas('subKegiatan.kegiatan', function ($query) use ($pegawai) {
                $query->where('id_penanggung_jawab', $pegawai->id_pegawai);
            })
            ->where(function ($query) {
                $query->where('status_dl', 'Ditolak')
                    ->orWhere('status_translok', 'Ditolak');
            })
            ->orderBy('tanggal_selesai', 'asc')
            ->get();
    }

    public function hasAnggotaContext(Pegawai $pegawai): bool
    {
        return $pegawai->active_role === 'Anggota Tim'
            || $pegawai->penugasanSebagaiAnggota()->exists()
            || $pegawai->hasRole('Anggota Tim');
    }

    public function hasKetuaContext(Pegawai $pegawai): bool
    {
        return $pegawai->active_role === 'Ketua Tim'
            || $pegawai->kegiatanYangDipimpin()->exists()
            || $pegawai->hasRole('Ketua Tim');
    }

    public function summaryAsAnggota(Pegawai $pegawai): array
    {
        return [
            'role_context' => 'anggota',
            'role_label' => 'Anggota Tim',
            'revision_label' => 'Revisi Ketua Tim',
            'revisi' => $this->revisiAsAnggota($pegawai)->count(),
            'sedang_berjalan' => $this->berjalanAsAnggota($pegawai)->count(),
            'sudah_terlewat' => $this->terlewatAsAnggota($pegawai)->count(),
        ];
    }

    public function summaryAsKetua(Pegawai $pegawai): array
    {
        return [
            'role_context' => 'ketua_tim',
            'role_label' => 'Ketua Tim',
            'revision_label' => 'Revisi Tanggal DL',
            'revisi' => $this->revisiAsKetua($pegawai)->count(),
            'sedang_berjalan' => $this->berjalanAsKetua($pegawai)->count(),
            'sudah_terlewat' => $this->terlewatAsKetua($pegawai)->count(),
        ];
    }

    public function monthlySummaryAsAnggota(Pegawai $pegawai, Carbon $periodEnd): array
    {
        $baseQuery = $this->monthlyAsAnggota($pegawai, $periodEnd);

        return [
            'role_context' => 'anggota',
            'role_label' => 'Anggota Tim',
            'period_label' => $periodEnd->translatedFormat('F Y'),
            'revisi' => $this->monthlyRevisiAsAnggota($pegawai, $periodEnd),
            'selesai' => $this->monthlySelesaiCount(clone $baseQuery, $periodEnd),
            'terlewat' => $this->monthlyTerlewatCount(clone $baseQuery, $periodEnd),
        ];
    }

    public function monthlySummaryAsKetua(Pegawai $pegawai, Carbon $periodEnd): array
    {
        $baseQuery = $this->monthlyAsKetua($pegawai, $periodEnd);

        return [
            'role_context' => 'ketua_tim',
            'role_label' => 'Ketua Tim',
            'period_label' => $periodEnd->translatedFormat('F Y'),
            'revisi' => $this->monthlyRevisiAsKetua($pegawai, $periodEnd),
            'selesai' => $this->monthlySelesaiCount(clone $baseQuery, $periodEnd),
            'terlewat' => $this->monthlyTerlewatCount(clone $baseQuery, $periodEnd),
        ];
    }

    private function monthlyAsAnggota(Pegawai $pegawai, Carbon $periodEnd): Builder
    {
        return $this->tasksDueInMonth($periodEnd)
            ->where('id_anggota', $pegawai->id_pegawai);
    }

    private function monthlyAsKetua(Pegawai $pegawai, Carbon $periodEnd): Builder
    {
        return $this->tasksDueInMonth($periodEnd)
            ->whereHas('subKegiatan.kegiatan', function ($query) use ($pegawai) {
                $query->where('id_penanggung_jawab', $pegawai->id_pegawai);
            });
    }

    private function tasksDueInMonth(Carbon $periodEnd): Builder
    {
        return Penugasan::query()
            ->whereBetween('tanggal_selesai', [
                $periodEnd->copy()->startOfMonth()->toDateString(),
                $periodEnd->toDateString(),
            ]);
    }

    private function monthlySelesaiCount(Builder $query, Carbon $periodEnd): int
    {
        return $query->whereHas('pengirimans.penerimaan', function ($penerimaan) use ($periodEnd) {
            $penerimaan->where('status', 'Diterima')
                ->where('penerimaans.created_at', '<=', $periodEnd);
        })->count();
    }

    private function monthlyTerlewatCount(Builder $query, Carbon $periodEnd): int
    {
        return $query->whereDoesntHave('pengirimans.penerimaan', function ($penerimaan) use ($periodEnd) {
            $penerimaan->where('status', 'Diterima')
                ->where('penerimaans.created_at', '<=', $periodEnd);
        })->count();
    }

    private function monthlyRevisiAsAnggota(Pegawai $pegawai, Carbon $periodEnd): int
    {
        return $this->monthlyAsAnggota($pegawai, $periodEnd)
            ->with(['pengirimans' => function ($pengirimans) use ($periodEnd) {
                $pengirimans->where('created_at', '<=', $periodEnd)
                    ->latest('created_at')
                    ->with(['penerimaan' => function ($penerimaan) use ($periodEnd) {
                        $penerimaan->where('created_at', '<=', $periodEnd);
                    }]);
            }])
            ->whereDoesntHave('pengirimans.penerimaan', function ($penerimaan) use ($periodEnd) {
                $penerimaan->where('status', 'Diterima')
                    ->where('penerimaans.created_at', '<=', $periodEnd);
            })
            ->get()
            ->filter(function (Penugasan $penugasan) {
                return $penugasan->pengirimans->first()?->penerimaan?->status === 'Revisi';
            })
            ->count();
    }

    private function monthlyRevisiAsKetua(Pegawai $pegawai, Carbon $periodEnd): int
    {
        return $this->monthlyAsKetua($pegawai, $periodEnd)
            ->where(function ($query) {
                $query->where('status_dl', 'Ditolak')
                    ->orWhere('status_translok', 'Ditolak');
            })
            ->count();
    }

    private function todoRelations(): array
    {
        return [
            'subKegiatan.kegiatan.bidang',
            'jenisKegiatan',
            'anggota',
            'kalenderDLs',
            'pengirimans.penerimaan',
        ];
    }
}
