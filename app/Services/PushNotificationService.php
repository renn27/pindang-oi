<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\Penugasan;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Notifications\GenericWebPushNotification;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class PushNotificationService
{
    public function notifyAnnouncementCreated(string $title): void
    {
        $pegawais = Pegawai::query()->get();

        $this->notifyMany(
            $pegawais,
            'Pengumuman baru',
            "Ada pengumuman baru: {$title}",
            route('announcements.pegawai'),
            'announcement-created-'.Str::slug($title).'-'.now()->timestamp,
            'umum'
        );
    }

    public function notifyAnnouncementReactivated(string $title): void
    {
        $pegawais = Pegawai::query()->get();

        $this->notifyMany(
            $pegawais,
            'Pengumuman aktif kembali',
            "Pengumuman aktif kembali: {$title}",
            route('announcements.pegawai'),
            'announcement-reactivated-'.now()->timestamp,
            'umum'
        );
    }

    public function notifyKegiatanDeleted(Kegiatan $kegiatan): void
    {
        $kegiatan->loadMissing(['penanggungJawab', 'subKegiatans.penugasans.anggota']);

        $anggotas = $kegiatan->subKegiatans
            ->flatMap(fn (SubKegiatan $subKegiatan) => $subKegiatan->penugasans)
            ->pluck('anggota');

        $this->notifyPegawai(
            $kegiatan->penanggungJawab,
            'Kegiatan dihapus',
            'Kegiatan dihapus: '.$this->kegiatanLabel($kegiatan),
            route('dashboard'),
            'kegiatan-deleted-'.$kegiatan->id_kegiatan,
            'ketua_tim'
        );

        $this->notifyMany(
            $anggotas,
            'Kegiatan dihapus',
            'Kegiatan dihapus: '.$this->kegiatanLabel($kegiatan),
            route('dashboard'),
            'kegiatan-deleted-'.$kegiatan->id_kegiatan,
            'anggota'
        );
    }

    public function notifySubKegiatanDeleted(SubKegiatan $subKegiatan): void
    {
        $subKegiatan->loadMissing(['kegiatan.penanggungJawab', 'penugasans.anggota']);

        $this->notifyPegawai(
            $subKegiatan->kegiatan?->penanggungJawab,
            'Sub kegiatan dihapus',
            'Sub kegiatan dihapus: '.$this->subKegiatanLabel($subKegiatan),
            route('dashboard'),
            'sub-kegiatan-deleted-'.$subKegiatan->id_sub_kegiatan,
            'ketua_tim'
        );

        $this->notifyMany(
            $subKegiatan->penugasans->pluck('anggota'),
            'Sub kegiatan dihapus',
            'Sub kegiatan dihapus: '.$this->subKegiatanLabel($subKegiatan),
            route('dashboard'),
            'sub-kegiatan-deleted-'.$subKegiatan->id_sub_kegiatan,
            'anggota'
        );
    }

    public function notifyPenugasanCreated(Penugasan $penugasan): void
    {
        $penugasan->loadMissing(['anggota', 'subKegiatan.kegiatan']);

        $this->notifyPegawai(
            $penugasan->anggota,
            'Penugasan baru',
            'Anda mendapat penugasan baru: '.$this->penugasanLabel($penugasan),
            $this->subKegiatanUrl($penugasan),
            'penugasan-created-'.$penugasan->id_penugasan,
            'anggota'
        );

        $this->notifyPendingTravelApproval($penugasan);
    }

    public function notifyPenugasanUpdated(Penugasan $penugasan): void
    {
        $penugasan->loadMissing(['anggota', 'subKegiatan.kegiatan']);

        $this->notifyPegawai(
            $penugasan->anggota,
            'Penugasan diperbarui',
            'Detail penugasan Anda diperbarui: '.$this->penugasanLabel($penugasan),
            $this->subKegiatanUrl($penugasan),
            'penugasan-updated-'.$penugasan->id_penugasan,
            'anggota'
        );

        $this->notifyPendingTravelApproval($penugasan);
    }

    public function notifyPenugasanDeleted(Penugasan $penugasan): void
    {
        $penugasan->loadMissing(['anggota', 'subKegiatan.kegiatan']);

        $this->notifyPegawai(
            $penugasan->anggota,
            'Penugasan dibatalkan',
            'Penugasan Anda dibatalkan/dihapus: '.$this->penugasanLabel($penugasan),
            route('dashboard'),
            'penugasan-deleted-'.$penugasan->id_penugasan,
            'anggota'
        );
    }


    public function notifyPengirimanSubmitted(Penugasan $penugasan): void
    {
        $penugasan->loadMissing(['anggota', 'subKegiatan.kegiatan.penanggungJawab']);

        $this->notifyPegawai(
            $penugasan->subKegiatan?->kegiatan?->penanggungJawab,
            'Pengiriman hasil kerja',
            ($penugasan->anggota?->nama_pegawai ?? 'Anggota tim').' mengirim hasil kerja: '.$this->penugasanLabel($penugasan),
            $this->subKegiatanUrl($penugasan),
            'pengiriman-submitted-'.$penugasan->id_penugasan,
            'ketua_tim'
        );
    }

    public function notifyPengirimanCancelled(Penugasan $penugasan): void
    {
        $penugasan->loadMissing(['anggota', 'subKegiatan.kegiatan.penanggungJawab']);

        $this->notifyPegawai(
            $penugasan->subKegiatan?->kegiatan?->penanggungJawab,
            'Pengiriman dibatalkan',
            ($penugasan->anggota?->nama_pegawai ?? 'Anggota tim').' membatalkan pengiriman hasil kerja.',
            $this->subKegiatanUrl($penugasan),
            'pengiriman-cancelled-'.$penugasan->id_penugasan,
            'ketua_tim'
        );
    }

    public function notifyPenerimaanResponded(Penugasan $penugasan, string $status): void
    {
        $penugasan->loadMissing(['anggota', 'subKegiatan.kegiatan']);

        $title = $status === 'Diterima' ? 'Hasil kerja diterima' : 'Hasil kerja perlu revisi';
        $body = $status === 'Diterima'
            ? 'Hasil kerja Anda diterima: '.$this->penugasanLabel($penugasan)
            : 'Hasil kerja Anda perlu revisi: '.$this->penugasanLabel($penugasan);

        $this->notifyPegawai(
            $penugasan->anggota,
            $title,
            $body,
            $this->subKegiatanUrl($penugasan),
            'penerimaan-'.$status.'-'.$penugasan->id_penugasan,
            'anggota'
        );
    }

    public function notifyPenerimaanCancelled(Penugasan $penugasan): void
    {
        $penugasan->loadMissing(['anggota', 'subKegiatan.kegiatan']);

        $this->notifyPegawai(
            $penugasan->anggota,
            'Penerimaan dibatalkan',
            'Penerimaan hasil kerja Anda dibatalkan. Silakan cek kembali penugasan.',
            $this->subKegiatanUrl($penugasan),
            'penerimaan-cancelled-'.$penugasan->id_penugasan,
            'anggota'
        );
    }

    public function notifyTravelDecision(Penugasan $penugasan, string $type, string $status): void
    {
        $penugasan->loadMissing(['anggota', 'subKegiatan.kegiatan.penanggungJawab']);

        $label = $type === 'dl' ? 'DL' : 'Translok';
        $body = "Pengajuan {$label} untuk {$this->penugasanLabel($penugasan)} {$this->statusText($status)}.";

        $this->notifyPegawai(
            $penugasan->anggota,
            "Status {$label} diperbarui",
            $body,
            route('master-kegiatan.index_rk_dl'),
            "travel-{$type}-{$status}-".$penugasan->id_penugasan,
            'anggota'
        );

        $this->notifyPegawai(
            $penugasan->subKegiatan?->kegiatan?->penanggungJawab,
            "Status {$label} diperbarui",
            $body,
            route('master-kegiatan.index_rk_dl'),
            "travel-{$type}-{$status}-".$penugasan->id_penugasan,
            'ketua_tim'
        );
    }

    public function notifyPendingTravelApproval(Penugasan $penugasan, bool $resubmitted = false): void
    {
        $penugasan->loadMissing(['anggota', 'subKegiatan.kegiatan']);

        foreach ($this->pendingTravelTypes($penugasan) as $type) {
            $label = $type === 'dl' ? 'DL' : 'Translok';
            $prefix = $resubmitted ? 'Pengajuan ulang' : 'Pengajuan';

            $this->notifyRole(
                'Pimpinan',
                "{$prefix} {$label}",
                "{$prefix} {$label} menunggu persetujuan untuk ".($penugasan->anggota?->nama_pegawai ?? 'pegawai').'.',
                route('master-kegiatan.index_rk_dl'),
                "travel-pending-{$type}-".$penugasan->id_penugasan,
                'pimpinan'
            );
        }
    }

    public function notifyPegawai(?Pegawai $pegawai, string $title, string $body, string $url, string $tag, string $roleContext = 'umum'): void
    {
        if (!$pegawai) {
            return;
        }

        $this->notifyMany(collect([$pegawai]), $title, $body, $url, $tag, $roleContext);
    }

    public function notifyRole(string $roleName, string $title, string $body, string $url, string $tag, string $roleContext = 'umum'): void
    {
        $pegawais = Pegawai::whereHas('roles', function ($query) use ($roleName) {
            $query->where('nama_role', $roleName);
        })->get();

        $this->notifyMany($pegawais, $title, $body, $url, $tag, $roleContext);
    }

    public function notifyMany(Collection|EloquentCollection $pegawais, string $title, string $body, string $url, string $tag, string $roleContext = 'umum'): void
    {
        $dispatchId = (string) Str::uuid();
        $recipients = $pegawais
            ->filter(fn ($pegawai) => $pegawai instanceof Pegawai)
            ->unique('id_pegawai')
            ->values();

        if ($recipients->isEmpty()) {
            Log::info('Notification skipped because recipient list is empty.', [
                'dispatch_id' => $dispatchId,
                'title' => $title,
                'tag' => $tag,
                'role_context' => $roleContext,
            ]);

            return;
        }

        Log::info('Notification dispatch started.', [
            'dispatch_id' => $dispatchId,
            'title' => $title,
            'tag' => $tag,
            'role_context' => $roleContext,
            'recipient_count' => $recipients->count(),
        ]);

        $databaseNotificationIds = [];

        foreach ($recipients as $pegawai) {
            $databaseNotificationIds[$pegawai->id_pegawai] = $this->createDatabaseNotification(
                $pegawai,
                $title,
                $body,
                $url,
                $tag,
                $roleContext,
                $dispatchId
            );
        }

        $pushRecipients = $recipients->filter(function (Pegawai $pegawai) use ($title, $tag, $roleContext) {
            $subscriptionCount = $pegawai->pushSubscriptions()->count();

            if ($subscriptionCount === 0) {
                Log::warning('Web push recipient has no active subscription.', [
                    'pegawai_id' => $pegawai->id_pegawai,
                    'nama_pegawai' => $pegawai->nama_pegawai,
                    'title' => $title,
                    'tag' => $tag,
                    'role_context' => $roleContext,
                ]);

                return false;
            }

            return true;
        });

        if ($pushRecipients->isEmpty()) {
            Log::warning('Web push skipped because no recipients have active subscriptions.', [
                'dispatch_id' => $dispatchId,
                'title' => $title,
                'tag' => $tag,
                'role_context' => $roleContext,
                'recipient_count' => $recipients->count(),
            ]);

            return;
        }

        foreach ($pushRecipients as $pegawai) {
            $databaseNotificationId = $databaseNotificationIds[$pegawai->id_pegawai] ?? null;
            $clickUrl = $databaseNotificationId
                ? route('notifications.read', $databaseNotificationId)
                : $url;

            try {
                $subscriptionCount = $pegawai->pushSubscriptions()->count();

                $notification = new GenericWebPushNotification($title, $body, $clickUrl, $tag, $databaseNotificationId);

                app()->terminating(function () use ($pegawai, $notification, $dispatchId, $title, $tag, $roleContext) {
                    try {
                        Notification::sendNow($pegawai, $notification);
                    } catch (\Throwable $e) {
                        Log::warning('Web push notification dispatch failed after response.', [
                            'dispatch_id' => $dispatchId,
                            'message' => $e->getMessage(),
                            'pegawai_id' => $pegawai->id_pegawai,
                            'nama_pegawai' => $pegawai->nama_pegawai,
                            'title' => $title,
                            'tag' => $tag,
                            'role_context' => $roleContext,
                        ]);
                    }
                });

                Log::info('Web push dispatch attempted for recipient.', [
                    'dispatch_id' => $dispatchId,
                    'pegawai_id' => $pegawai->id_pegawai,
                    'nama_pegawai' => $pegawai->nama_pegawai,
                    'subscription_count' => $subscriptionCount,
                    'database_notification_id' => $databaseNotificationId,
                    'title' => $title,
                    'tag' => $tag,
                    'role_context' => $roleContext,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Web push notification dispatch failed.', [
                    'dispatch_id' => $dispatchId,
                    'message' => $e->getMessage(),
                    'pegawai_id' => $pegawai->id_pegawai,
                    'nama_pegawai' => $pegawai->nama_pegawai,
                    'title' => $title,
                    'tag' => $tag,
                    'role_context' => $roleContext,
                ]);
            }
        }

        Log::info('Notification dispatch finished.', [
            'dispatch_id' => $dispatchId,
            'title' => $title,
            'tag' => $tag,
            'role_context' => $roleContext,
            'recipient_count' => $recipients->count(),
            'push_recipient_count' => $pushRecipients->count(),
        ]);
    }

    private function createDatabaseNotification(Pegawai $pegawai, string $title, string $body, string $url, string $tag, string $roleContext, string $dispatchId): ?string
    {
        $notificationId = (string) Str::uuid();

        try {
            $existingNotification = $this->findRecentEquivalentNotification($pegawai, $tag, $roleContext);

            if ($existingNotification) {
                Log::info('Database notification reused for equivalent recent notification.', [
                    'dispatch_id' => $dispatchId,
                    'pegawai_id' => $pegawai->id_pegawai,
                    'nama_pegawai' => $pegawai->nama_pegawai,
                    'notification_id' => $existingNotification->id,
                    'title' => $title,
                    'tag' => $tag,
                    'role_context' => $roleContext,
                ]);

                return $existingNotification->id;
            }

            $pegawai->notifications()->create([
                'id' => $notificationId,
                'type' => GenericWebPushNotification::class,
                'data' => [
                    'title' => $title,
                    'body' => $body,
                    'url' => $url,
                    'tag' => $tag,
                    'role_context' => $roleContext,
                ],
            ]);

            return $notificationId;
        } catch (\Throwable $e) {
            Log::warning('Database notification creation failed.', [
                'dispatch_id' => $dispatchId,
                'message' => $e->getMessage(),
                'pegawai_id' => $pegawai->id_pegawai,
                'nama_pegawai' => $pegawai->nama_pegawai,
                'title' => $title,
                'tag' => $tag,
                'role_context' => $roleContext,
            ]);
        }

        return null;
    }

    private function findRecentEquivalentNotification(Pegawai $pegawai, string $tag, string $roleContext): ?object
    {
        if (! str_starts_with($tag, 'travel-pending-')) {
            return null;
        }

        return $pegawai->notifications()
            ->where('created_at', '>=', now()->subMinutes(10))
            ->get()
            ->first(function ($notification) use ($tag, $roleContext) {
                $data = $notification->data ?? [];

                return ($data['tag'] ?? null) === $tag
                    && ($data['role_context'] ?? null) === $roleContext;
            });
    }

    private function pendingTravelTypes(Penugasan $penugasan): array
    {
        $types = [];

        if ((int) $penugasan->butuh_dl === 1 && $penugasan->status_dl === 'Menunggu') {
            $types[] = 'dl';
        }

        if ((int) $penugasan->butuh_translok === 1 && $penugasan->status_translok === 'Menunggu') {
            $types[] = 'translok';
        }

        return $types;
    }

    private function penugasanLabel(Penugasan $penugasan): string
    {
        return $penugasan->subKegiatan?->nama_sub_kegiatan
            ?? $penugasan->subKegiatan?->kegiatan?->nama_rk_kegiatan
            ?? 'penugasan';
    }

    private function kegiatanLabel(Kegiatan $kegiatan): string
    {
        return $kegiatan->nama_rk_kegiatan ?? 'kegiatan';
    }

    private function subKegiatanLabel(SubKegiatan $subKegiatan): string
    {
        return $subKegiatan->nama_sub_kegiatan
            ?? $subKegiatan->kegiatan?->nama_rk_kegiatan
            ?? 'sub kegiatan';
    }

    private function subKegiatanUrl(Penugasan $penugasan): string
    {
        if ($penugasan->subKegiatan?->kegiatan) {
            return route('sub.kegiatan.show', [
                'kegiatan' => $penugasan->subKegiatan->kegiatan->id_kegiatan,
                'subKegiatan' => $penugasan->subKegiatan->id_sub_kegiatan,
            ]);
        }

        return route('dashboard');
    }

    private function statusText(string $status): string
    {
        return match ($status) {
            'ACC' => 'disetujui',
            'Ditolak' => 'ditolak',
            default => 'diperbarui',
        };
    }
}
