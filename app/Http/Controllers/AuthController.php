<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Rejestracja tworzy konto klienta wraz z automatycznie wygenerowanym rachunkiem.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request) {
            $clientRole = Role::where('name', Role::CLIENT)->firstOrFail();

            $user = User::create([
                'role_id' => $clientRole->id,
                'first_name' => $request->validated('first_name'),
                'last_name' => $request->validated('last_name'),
                'email' => $request->validated('email'),
                'pesel' => $request->validated('pesel'),
                'password' => $request->validated('password'),
            ]);

            $user->account()->create([
                'number' => Account::generateUniqueNumber(),
                'balance' => 0,
                'currency' => config('bank.currency', 'PLN'),
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Konto zostało utworzone. Witamy w bankowości internetowej!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Zostałeś wylogowany.');
    }
}
