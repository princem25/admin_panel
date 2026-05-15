<?php

namespace App\Http\Controllers;

use App\Services\User\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        try {
            $notifications = $this->notificationService->getNotifications(Auth::user());
            return view('notifications.index', compact('notifications'));
        } catch (\Exception $e) {
            Log::error('NotificationController@index error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Return only unread notifications.
     */
    public function unread()
    {
        try {
            $notifications = $this->notificationService->getUnreadNotifications(Auth::user());
            return view('notifications.index', compact('notifications'));
        } catch (\Exception $e) {
            Log::error('NotificationController@unread error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Mark a single notification as read and redirect to the stored URL.
     */
    public function markAsRead($id)
    {
        try {
            $url = $this->notificationService->markAsRead(Auth::user(), $id);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'redirect' => $url]);
            }

            return redirect($url);
        } catch (\Exception $e) {
            Log::error('NotificationController@markAsRead error', ['error' => $e->getMessage()]);
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to mark notification as read.'], 500);
            }
            return back()->with('error', 'Failed to mark notification as read.');
        }
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        try {
            $this->notificationService->markAllAsRead(Auth::user());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true]);
            }

            return back()->with('success', 'All notifications marked as read.');
        } catch (\Exception $e) {
            Log::error('NotificationController@markAllAsRead error', ['error' => $e->getMessage()]);
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to mark all notifications as read.'], 500);
            }
            return back()->with('error', 'Failed to mark all notifications as read.');
        }
    }
}

