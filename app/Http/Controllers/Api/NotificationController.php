<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get user's notifications list and unread count.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = Notifikasi::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        $unreadCount = Notifikasi::where('user_id', $user->id)
            ->where('dibaca', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        $notif = Notifikasi::where('user_id', $user->id)->find($id);

        if ($notif) {
            $notif->update(['dibaca' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai telah dibaca.',
        ]);
    }

    /**
     * Mark all notifications of the user as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        Notifikasi::where('user_id', $user->id)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai telah dibaca.',
        ]);
    }
}
