<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        $user->load('profile');

        return view('profile.show', compact('user'));
    }

    public function edit(Request $request): View
    {
        $user = $request->user()->load('profile');

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'display_name' => 'sometimes|string|max:255',
            'bio' => 'sometimes|nullable|string',
            'avatar_url' => 'sometimes|nullable|url',
        ]);

        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return back()->with('success', 'Profile updated.');
    }
}
