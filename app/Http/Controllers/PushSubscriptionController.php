<?php

namespace App\Http\Controllers;

use App\Notifications\GenericWebPushNotification;
use App\Services\TodoReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class PushSubscriptionController extends Controller
{
    public function publicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key'),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'max:500'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
            'contentEncoding' => ['nullable', 'string'],
        ]);

        $request->user()->updatePushSubscription(
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth'],
            $validated['contentEncoding'] ?? 'aes128gcm'
        );

        if (is_null($request->user()->todo_reminder_enabled)) {
            $request->user()->update(['todo_reminder_enabled' => true]);
        }

        return response()->json([
            'success' => true,
            'subscription_count' => $request->user()->pushSubscriptions()->count(),
            'todo_reminder_enabled' => (bool) $request->user()->todo_reminder_enabled,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'max:500'],
        ]);

        $request->user()->deletePushSubscription($validated['endpoint']);

        return response()->json(['success' => true]);
    }

    public function test(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['nullable', 'string', 'max:500'],
        ]);

        $query = $request->user()->pushSubscriptions();

        if (! empty($validated['endpoint'])) {
            $query->where('endpoint', $validated['endpoint']);
        }

        $subscriptions = $query->get();

        if ($subscriptions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Browser ini belum memiliki subscription aktif untuk akun ini.',
            ], 422);
        }

        $request->user()->setRelation('pushSubscriptions', $subscriptions);

        Notification::sendNow(
            $request->user(),
            new GenericWebPushNotification(
                'Tes notifikasi Pindang OI',
                'Jika notifikasi ini muncul, web push browser sudah aktif.',
                route('dashboard'),
                'webpush-test-'.now()->timestamp
            )
        );

        return response()->json(['success' => true]);
    }

    public function todoReminderPreference(Request $request): JsonResponse
    {
        if (is_null($request->user()->todo_reminder_enabled)
            && $request->user()->pushSubscriptions()->exists()) {
            $request->user()->update(['todo_reminder_enabled' => true]);
        }

        return response()->json([
            'enabled' => (bool) $request->user()->todo_reminder_enabled,
        ]);
    }

    public function updateTodoReminderPreference(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $request->user()->update([
            'todo_reminder_enabled' => $validated['enabled'],
        ]);

        return response()->json([
            'success' => true,
            'enabled' => (bool) $request->user()->todo_reminder_enabled,
        ]);
    }

    public function testTodoReminder(Request $request, TodoReminderService $reminders): JsonResponse
    {
        $sent = $reminders->sendTestReminder($request->user());

        if ($sent === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memiliki konteks To Do List Anggota Tim atau Ketua Tim.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'sent' => $sent,
        ]);
    }

}
