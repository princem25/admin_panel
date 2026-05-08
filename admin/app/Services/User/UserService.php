<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Session;

class UserService
{
    /**
     * Set the preferred locale for the user and session.
     *
     * @param \App\Models\User|null $user
     * @param string $locale
     */
    public function setLocale($user, string $locale): void
    {
        if (in_array($locale, ['en', 'ar'])) {
            Session::put('locale', $locale);

            if ($user) {
                $user->preferred_locale = $locale;
                $user->save();
            }
        }
    }
}
