<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Contracts\Translation\HasLocalePreference;
use App\Notifications\ProductLowStock;

class User extends Authenticatable implements HasLocalePreference
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the user's preferred locale.
     *
     * @return string
     */
    public function preferredLocale()
    {
        return $this->preferred_locale ?? config('app.locale');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Products this user is on the waitlist for.
     */
    public function waitlistProducts()
    {
        return $this->belongsToMany(Product::class, 'product_waitlists')
            ->withTimestamps();
    }
    /**
     * Route notifications for the webhook channel.
     *
     * @return string|null
     */
    public function routeNotificationForWebhook()
    {
        return $this->webhook_url;
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @return string|null
     */
    public function routeNotificationForSlack($notification = null)
    {
        if ($notification instanceof ProductLowStock) {
            return config('services.slack.alerts_webhook');
        }
  
        return config('services.slack.orders_webhook');
    }
}
