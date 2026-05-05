<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications (Latest first).
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Return only unread notifications.
     */
    public function unread()
    {
        $notifications = Auth::user()->unreadNotifications()->latest()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read and redirect to the stored URL.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        if ($notification->unread()) {
            $notification->markAsRead();
            
            // Invalidate unread count cache
            Cache::forget('unread_count_' . Auth::id());
        }

        $url = $notification->data['url'] ?? route('notifications.index');

        if (request()->ajax()) {
            return response()->json(['success' => true, 'redirect' => $url]);
        }

        return redirect($url);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        // Invalidate unread count cache
        Cache::forget('unread_count_' . Auth::id());

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }
}
