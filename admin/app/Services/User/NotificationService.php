<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    /**
     * Get paginated notifications for the user.
     *
     * @param User $user
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getNotifications($user)
    {
        return $user->notifications()->latest()->paginate(15);
    }

    /**
     * Get paginated unread notifications for the user.
     *
     * @param User $user
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUnreadNotifications($user)
    {
        return $user->unreadNotifications()->latest()->paginate(15);
    }

    /**
     * Mark a single notification as read and return the redirect URL.
     *
     * @param User $user
     * @param string $id
     * @return string
     */
    public function markAsRead($user, $id): string
    {
        $notification = $user->notifications()->findOrFail($id);
        
        if ($notification->unread()) {
            $notification->markAsRead();
            
            // Invalidate unread count cache
            Cache::forget('unread_count_' . $user->id);
        }

        return $notification->data['url'] ?? route('notifications.index');
    }

    /**
     * Mark all unread notifications as read.
     *
     * @param User $user
     */
    public function markAllAsRead($user): void
    {
        $user->unreadNotifications->markAsRead();

        // Invalidate unread count cache
        Cache::forget('unread_count_' . $user->id);
    }
}
