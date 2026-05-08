<?php

namespace App\Http\Controllers;

use App\Services\User\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of notifications (Latest first).
     */
    public function index()
    {
        $notifications = $this->notificationService->getNotifications(Auth::user());
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Return only unread notifications.
     */
    public function unread()
    {
        $notifications = $this->notificationService->getUnreadNotifications(Auth::user());
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read and redirect to the stored URL.
     */
    public function markAsRead($id)
    {
        $url = $this->notificationService->markAsRead(Auth::user(), $id);

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
        $this->notificationService->markAllAsRead(Auth::user());

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }
}

