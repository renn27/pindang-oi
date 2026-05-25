<?php

namespace App\Services;

use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TodoReminderService
{
    public function __construct(
        private readonly TodoListService $todoList,
        private readonly PushNotificationService $notifications,
    ) {
    }

    public function sendScheduledReminders(): int
    {
        return $this->sendToEnabledPegawais(
            fn (Pegawai $pegawai) => $this->sendForPegawai($pegawai)
        );
    }

    public function sendScheduledMonthlyRecaps(): int
    {
        $periodEnd = now('Asia/Jakarta')->subMonthNoOverflow()->endOfMonth();

        return $this->sendToEnabledPegawais(
            fn (Pegawai $pegawai) => $this->sendMonthlyForPegawai($pegawai, $periodEnd)
        );
    }

    public function sendTestReminder(Pegawai $pegawai): int
    {
        $pegawai->loadMissing('roles');

        return $this->sendForPegawai($pegawai, true);
    }

    public function sendForPegawai(Pegawai $pegawai, bool $isTest = false): int
    {
        $summaries = $this->summariesFor($pegawai);

        foreach ($summaries as $summary) {
            $suffix = $isTest ? ' (Tes)' : '';
            $this->notifications->notifyPegawai(
                $pegawai,
                "Pengingat To Do List - {$summary['role_label']}{$suffix}",
                $this->messageFor($summary),
                route('dashboard'),
                $this->tagFor($summary['role_context'], $isTest),
                $summary['role_context'],
            );
        }

        return $summaries->count();
    }

    public function sendMonthlyForPegawai(Pegawai $pegawai, Carbon $periodEnd): int
    {
        $summaries = $this->monthlySummariesFor($pegawai, $periodEnd);

        foreach ($summaries as $summary) {
            $this->notifications->notifyPegawai(
                $pegawai,
                "Rekap To Do List {$summary['period_label']} - {$summary['role_label']}",
                $this->monthlyMessageFor($summary),
                route('dashboard'),
                "todo-monthly-recap-{$summary['role_context']}-".$periodEnd->format('Ym'),
                $summary['role_context'],
            );
        }

        return $summaries->count();
    }

    private function summariesFor(Pegawai $pegawai): Collection
    {
        $summaries = collect();

        if ($this->todoList->hasAnggotaContext($pegawai)) {
            $summaries->push($this->todoList->summaryAsAnggota($pegawai));
        }

        if ($this->todoList->hasKetuaContext($pegawai)) {
            $summaries->push($this->todoList->summaryAsKetua($pegawai));
        }

        return $summaries;
    }

    private function monthlySummariesFor(Pegawai $pegawai, Carbon $periodEnd): Collection
    {
        $summaries = collect();

        if ($this->todoList->hasAnggotaContext($pegawai)) {
            $summaries->push($this->todoList->monthlySummaryAsAnggota($pegawai, $periodEnd));
        }

        if ($this->todoList->hasKetuaContext($pegawai)) {
            $summaries->push($this->todoList->monthlySummaryAsKetua($pegawai, $periodEnd));
        }

        return $summaries;
    }

    private function messageFor(array $summary): string
    {
        $total = $summary['revisi'] + $summary['sedang_berjalan'] + $summary['sudah_terlewat'];

        if ($total === 0) {
            return "Selamat! Semua tugas {$summary['role_label']} aman hari ini.\n"
                .'Selengkapnya lihat di Dashboard.';
        }

        $revisionLabel = $summary['role_context'] === 'ketua_tim' ? 'Revisi DL' : 'Revisi Ketua';

        return "{$revisionLabel}: {$summary['revisi']} | Berjalan: {$summary['sedang_berjalan']} | Terlewat: {$summary['sudah_terlewat']}\n"
            .'Selengkapnya lihat di Dashboard.';
    }

    private function tagFor(string $roleContext, bool $isTest): string
    {
        $prefix = $isTest ? 'todo-reminder-test' : 'todo-reminder';

        return "{$prefix}-{$roleContext}-".now('Asia/Jakarta')->format('Ymd-Hi');
    }

    private function monthlyMessageFor(array $summary): string
    {
        $revisionLabel = $summary['role_context'] === 'ketua_tim' ? 'Revisi DL' : 'Revisi Ketua';

        return "{$revisionLabel}: {$summary['revisi']} | Selesai: {$summary['selesai']} | Terlewat: {$summary['terlewat']}\n"
            .'Selengkapnya lihat di Dashboard.';
    }

    private function enabledPegawaisQuery(): Builder
    {
        return Pegawai::query()
            ->active()
            ->with('roles')
            ->where(function ($query) {
                $query->where('todo_reminder_enabled', true)
                    ->orWhere(function ($defaultEnabled) {
                        $defaultEnabled->whereNull('todo_reminder_enabled')
                            ->whereHas('pushSubscriptions');
                    });
            });
    }

    private function sendToEnabledPegawais(callable $send): int
    {
        $sent = 0;

        $this->enabledPegawaisQuery()
            ->chunk(100, function ($pegawais) use (&$sent, $send) {
                foreach ($pegawais as $pegawai) {
                    $sent += $send($pegawai);
                }
            });

        return $sent;
    }
}
