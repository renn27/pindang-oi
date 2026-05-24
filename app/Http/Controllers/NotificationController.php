<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, string $notification): RedirectResponse
    {
        $notification = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        $notification->markAsRead();

        $targetUrl = $notification->data['url'] ?? route('dashboard');

        return redirect($targetUrl);
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }

    public function latestUnread(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'since' => ['nullable', 'date'],
        ]);

        $query = $request->user()
            ->unreadNotifications()
            ->latest()
            ->limit(5);

        if (! empty($validated['since'])) {
            $query->where('created_at', '>', $validated['since']);
        }

        $notifications = $query->get()
            ->reverse()
            ->values()
            ->map(function ($notification) {
                $data = $notification->data ?? [];

                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'Notifikasi',
                    'body' => $data['body'] ?? 'Ada pembaruan baru.',
                    'tag' => $data['tag'] ?? 'pindang-oi',
                    'role_context' => $data['role_context'] ?? 'umum',
                    'created_at' => $notification->created_at?->toJSON(),
                    'read_url' => route('notifications.read', $notification->id),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'server_time' => now()->toJSON(),
        ]);
    }
}
