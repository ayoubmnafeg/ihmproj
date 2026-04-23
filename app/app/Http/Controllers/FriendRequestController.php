<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FriendRequestController extends Controller
{
    public function index(Request $request): View
    {
        $incomingRequests = FriendRequest::with('sender.profile')
            ->where('receiver_id', $request->user()->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $sentRequests = FriendRequest::with('receiver.profile')
            ->where('sender_id', $request->user()->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $friends = FriendRequest::with(['sender.profile', 'receiver.profile'])
            ->where('status', 'accepted')
            ->where(function ($query) use ($request) {
                $query->where('sender_id', $request->user()->id)
                    ->orWhere('receiver_id', $request->user()->id);
            })
            ->latest('responded_at')
            ->get();

        return view('friends.requests', compact('incomingRequests', 'sentRequests', 'friends'));
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'You cannot send a friend request to yourself.');
        }

        $existingOutgoing = FriendRequest::where('sender_id', $request->user()->id)
            ->where('receiver_id', $user->id)
            ->first();

        if ($existingOutgoing) {
            if ($existingOutgoing->status === 'pending') {
                return back()->with('success', 'Friend request already sent.');
            }

            if ($existingOutgoing->status === 'accepted') {
                return back()->with('success', 'You are already friends.');
            }
        }

        $existingIncoming = FriendRequest::where('sender_id', $user->id)
            ->where('receiver_id', $request->user()->id)
            ->first();

        if ($existingIncoming && $existingIncoming->status === 'pending') {
            $existingIncoming->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            return back()->with('success', 'Friend request accepted.');
        }

        FriendRequest::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $user->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Friend request sent.');
    }

    public function update(Request $request, FriendRequest $friendRequest): RedirectResponse
    {
        $data = $request->validate([
            'action' => 'required|in:accepted,rejected,canceled',
        ]);

        if ($friendRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been handled.');
        }

        if (in_array($data['action'], ['accepted', 'rejected'], true) && $friendRequest->receiver_id !== $request->user()->id) {
            abort(403);
        }

        if ($data['action'] === 'canceled' && $friendRequest->sender_id !== $request->user()->id) {
            abort(403);
        }

        $friendRequest->update([
            'status' => $data['action'],
            'responded_at' => now(),
        ]);

        if ($data['action'] === 'accepted') {
            return back()->with('success', 'Friend request accepted.');
        }

        if ($data['action'] === 'rejected') {
            return back()->with('success', 'Friend request deleted.');
        }

        return back()->with('success', 'Friend request canceled.');
    }
}
