<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    /**
     * Switch the application locale.
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch($locale)
    {
        if (in_array($locale, ['en', 'ar'])) {
            Session::put('locale', $locale);

            if (Auth::check()) {
                $user = Auth::user();
                $user->preferred_locale = $locale;
                $user->save();
            }
        }

        return redirect()->back()->with('status', 'locale-updated');
    }
}
