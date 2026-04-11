<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Reaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    // Toggle reaction on any content (publication or comment)
    public function toggle(Request $request, Content $content): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|string|in:like,love,laugh,sad,angry',
        ]);

        $existing = Reaction::where('user_id', $request->user()->id)
            ->where('content_id', $content->id)
            ->first();

        if ($existing) {
            if ($existing->type === $data['type']) {
                // Same type — remove it
                $existing->delete();
                return response()->json(['message' => 'Reaction removed.']);
            }
            // Different type — update it
            $existing->update(['type' => $data['type']]);
            return response()->json($existing);
        }

        $reaction = Reaction::create([
            'type'       => $data['type'],
            'user_id'    => $request->user()->id,
            'content_id' => $content->id,
        ]);

        return response()->json($reaction, 201);
    }
}
