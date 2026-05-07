<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Moderator;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModeratorController extends Controller
{
    public function index(): View
    {
        $moderators = Moderator::with('user.profile')->get();
        $assignableUsers = User::with('profile')->latest()->take(100)->get();

        return view('admin.moderators', compact('moderators', 'assignableUsers'));
    }

    public function assign(Request $request): RedirectResponse
    {
        $data = $request->validate(['user_id' => 'required|uuid|exists:users,id']);
        Moderator::firstOrCreate(['user_id' => $data['user_id']]);

        return back()->with('success', 'Moderator assigned.');
    }

    public function remove(User $user): RedirectResponse
    {
        Moderator::where('user_id', $user->id)->delete();

        return back()->with('success', 'Moderator removed.');
    }
}
