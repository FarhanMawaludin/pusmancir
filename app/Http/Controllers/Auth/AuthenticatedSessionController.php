<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Ambil input username dan password mentah dari request
        $inputUsername = $request->input('username');
        $inputPassword = $request->input('password');

        // Cek jika username dan password yang diketik user sama persis
        if ($user->role === 'anggota' && $inputUsername === $inputPassword) {
            return redirect()->route('anggota.dashboard.index')
                ->with('force_password_change', 'Demi keamanan akun Anda, silakan ubah kata sandi karena masih sama dengan username.');
        }

        return match ($user->role) {
            'admin', 'pustakawan' => redirect()->route('admin.dashboard.index'),
            'anggota' => redirect()->route('anggota.dashboard.index'),
            default => redirect('/'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
