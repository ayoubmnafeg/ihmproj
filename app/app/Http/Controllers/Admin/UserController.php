<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ban;
use App\Models\User;
use App\Models\UserWarning;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::with('profile')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->paginate(30);

        return response()->json($users);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user->load(['profile', 'bans', 'warnings']));
    }

    public function ban(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'type'     => 'required|string|in:user,ip,device',
            'reason'   => 'required|string',
            'ends_at'  => 'nullable|date|after:now',
        ]);

        $ban = Ban::create([
            'type'      => $data['type'],
            'value'     => $user->id,
            'reason'    => $data['reason'],
            'starts_at' => now(),
            'ends_at'   => $data['ends_at'] ?? null,
            'user_id'   => $user->id,
            'banned_by' => $request->user()->id,
        ]);

        $user->update(['status' => 'banned']);

        return response()->json($ban, 201);
    }

    public function unban(Request $request, User $user): JsonResponse
    {
        Ban::where('user_id', $user->id)->whereNull('ends_at')->delete();
        $user->update(['status' => 'active']);

        return response()->json(['message' => 'User unbanned.']);
    }

    public function warn(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'message'  => 'required|string',
            'severity' => 'required|string|in:low,medium,high',
        ]);

        $warning = UserWarning::create([
            'message'   => $data['message'],
            'severity'  => $data['severity'],
            'user_id'   => $user->id,
            'issued_by' => $request->user()->id,
        ]);

        return response()->json($warning, 201);
    }
}
