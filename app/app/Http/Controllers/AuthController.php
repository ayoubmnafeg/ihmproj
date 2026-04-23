<?php

namespace App\Http\Controllers;

use App\Models\Profile;
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
        $data = $request->validate([
            'username' => 'required|string|max:30',
            'password' => 'required|string',
        ]);

        $profile = Profile::query()
            ->select(['id', 'user_id'])
            ->where('display_name', $data['username'])
            ->first();

        if (! $profile || ! Auth::attempt(['email' => $profile->user->email, 'password' => $data['password']], $request->boolean('remember'))) {
            return back()->withErrors(['username' => 'The provided credentials are incorrect.'])->withInput();
        }

        if (Auth::user()->status === 'banned') {
            Auth::logout();
            $request->session()->invalidate();

            return back()->withErrors(['username' => 'Your account has been banned.'])->withInput();
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
