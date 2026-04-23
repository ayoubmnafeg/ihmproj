<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ban;
use App\Models\User;
use App\Models\UserWarning;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $members = User::with('profile')->latest()->paginate(20);

        return view('admin.users', compact('members'));
    }

    public function show(User $user): View
    {
        $user->load('profile', 'bans', 'warnings');

        return view('admin.user-show', compact('user'));
    }

    public function ban(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);
        $user->update(['status' => 'banned']);

        Ban::create([
            'user_id' => $user->id,
            'banned_by' => $request->user()->id,
            'reason' => $data['reason'],
        ]);

        return back()->with('success', 'User banned.');
    }

    public function unban(User $user): RedirectResponse
    {
        $user->update(['status' => 'active']);

        return back()->with('success', 'User unbanned.');
    }

    public function warn(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);

        UserWarning::create([
            'user_id' => $user->id,
            'warned_by' => $request->user()->id,
            'reason' => $data['reason'],
        ]);

        return back()->with('success', 'Warning issued.');
    }
}
