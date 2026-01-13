<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = auth()->user();

        $user->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token saved',
        ]);
    }

    public function notifications(Request $request)
    {
        return response()->json(
            $request->user()
                ->notifications()
                ->latest()
                ->get()
        );
    }

    public function markNotificationRead($id, Request $request)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json(['status' => 'ok']);
    }

    public function deleteNotification($id, Request $request)
    {
        $request->user()
            ->notifications()
            ->where('id', $id)
            ->delete();

        return response()->json(['status' => 'deleted']);
    }
}
