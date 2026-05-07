<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.gate', ['initialPanel' => 'login']);
    }

    public function login(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:30',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('login')
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $data = $validator->validated();

        $profile = Profile::query()
            ->select(['id', 'user_id'])
            ->where('display_name', $data['username'])
            ->first();

        if (! $profile || ! Auth::attempt(['email' => $profile->user->email, 'password' => $data['password']], $request->boolean('remember'))) {
            return redirect()->route('login')
                ->withErrors(['username' => 'The provided credentials are incorrect.'])
                ->withInput($request->except('password'));
        }

        if (Auth::user()->status === 'banned') {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('login')
                ->withErrors(['username' => 'Your account has been banned.'])
                ->withInput($request->except('password'));
        }

        $request->session()->regenerate();

        $defaultRedirect = Auth::user()->isAdmin()
            ? route('admin.dashboard')
            : route('feed.index');

        return redirect()->intended($defaultRedirect);
    }

    public function showRegister(): View
    {
        return view('auth.gate', ['initialPanel' => 'register']);
    }

    public function register(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
            'display_name' => 'required|string|max:30|unique:profiles,display_name|regex:/^[a-zA-Z0-9_]+$/',
        ]);

        if ($validator->fails()) {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $data = $validator->validated();

        // Users still need a unique email for Laravel auth (login resolves profile → user by username).
        $email = strtolower($data['display_name']).'.'.uniqid('', true).'@reg.invalid';

        $user = User::create([
            'email' => $email,
            'password' => $data['password'],
            'status' => 'active',
        ]);

        $user->profile()->create([
            'display_name' => $data['display_name'],
            'gender' => null,
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
