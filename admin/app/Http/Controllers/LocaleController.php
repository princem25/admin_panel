<?php

namespace App\Http\Controllers;

use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LocaleController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Switch the application locale.
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch($locale)
    {
        try {
            $this->userService->setLocale(Auth::user(), $locale);

            return redirect()->back()->with('status', 'locale-updated');
        } catch (\Exception $e) {
            Log::error('LocaleController@switch error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update locale.');
        }
    }
}
