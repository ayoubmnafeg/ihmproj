<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Moderator;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModeratorController extends Controller
{
    public function index(): JsonResponse
    {
        $moderators = Moderator::with('user.profile')->get();

        return response()->json($moderators);
    }

    public function assign(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
        ]);

        $moderator = Moderator::firstOrCreate(
            ['user_id' => $data['user_id']],
            ['assigned_since' => now()]
        );

        return response()->json($moderator->load('user.profile'), 201);
    }

    public function remove(User $user): JsonResponse
    {
        Moderator::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Moderator role removed.']);
    }
}
