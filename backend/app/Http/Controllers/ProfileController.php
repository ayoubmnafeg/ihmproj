<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('profile'));
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'display_name' => 'sometimes|string|max:255',
            'bio'          => 'sometimes|nullable|string',
            'avatar_url'   => 'sometimes|nullable|url',
        ]);

        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json($request->user()->load('profile'));
    }
}
