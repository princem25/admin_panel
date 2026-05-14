<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        try {
            return view('auth.login');
        } catch (\Exception $e) {
            Log::error('Auth\AuthenticatedSessionController@create error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            $request->session()->regenerate();

            $user = Auth::user();

            // Role-based redirect
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard'); // normal user
        } catch (\Exception $e) {
            Log::error('Auth\AuthenticatedSessionController@store error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return redirect('/');
        } catch (\Exception $e) {
            Log::error('Auth\AuthenticatedSessionController@destroy error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
