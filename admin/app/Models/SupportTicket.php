<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'customer_name',
        'priority',
        'status',
        'assigned_user_id',
        'message',
        'admin_comment',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
