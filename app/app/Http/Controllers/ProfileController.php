<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        $user->load('profile');

        $friendRequestStatus = null;
        $outgoingPendingRequestId = null;

        if (auth()->id() !== $user->id) {
            $friendRequest = FriendRequest::where(function ($query) use ($user) {
                $query->where('sender_id', auth()->id())
                    ->where('receiver_id', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', auth()->id());
            })->first();

            if ($friendRequest) {
                if ($friendRequest->status === 'accepted') {
                    $friendRequestStatus = 'friends';
                } elseif ($friendRequest->status === 'pending') {
                    if ($friendRequest->sender_id === auth()->id()) {
                        $friendRequestStatus = 'outgoing_pending';
                        $outgoingPendingRequestId = $friendRequest->id;
                    } else {
                        $friendRequestStatus = 'incoming_pending';
                    }
                }
            }
        }

        return view('profile.show', compact('user', 'friendRequestStatus', 'outgoingPendingRequestId'));
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
