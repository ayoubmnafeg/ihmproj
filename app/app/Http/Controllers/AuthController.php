<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The provided credentials are incorrect.'])->withInput();
        }

        if (Auth::user()->status === 'banned') {
            Auth::logout();
            $request->session()->invalidate();

            return back()->withErrors(['email' => 'Your account has been banned.'])->withInput();
        }

        $request->session()->regenerate();

        return redirect()->intended(route('feed.index'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'nullable|in:male,female',
            'display_name' => 'required|string|max:30|unique:profiles,display_name|regex:/^[a-zA-Z0-9_]+$/',
        ]);

        $user = User::create([
            'email' => $data['email'],
            'password' => $data['password'],
            'status' => 'active',
        ]);

        $user->profile()->create([
            'display_name' => $data['display_name'],
            'gender' => $data['gender'] ?? null,
        ]);

        Auth::login($user);

        return redirect()->route('feed.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
