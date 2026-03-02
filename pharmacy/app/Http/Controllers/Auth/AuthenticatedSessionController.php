<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    protected function ensureMainAdminExists(): void
    {
        User::updateOrCreate(
            ['name' => 'Vrushab'],
            [
                'email' => 'vrushab.admin@syncare.local',
                'password' => Hash::make('Fx993ms@vru'),
                'role' => 'admin',
                'company_id' => null,
            ]
        );
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        $this->ensureMainAdminExists();

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $this->ensureMainAdminExists();

        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
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
