<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Reaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function toggle(Request $request, Content $content): RedirectResponse
    {
        $data = $request->validate([
            'type' => 'required|in:upvote,downvote',
        ]);

        $existing = Reaction::where('user_id', $request->user()->id)
            ->where('content_id', $content->id)
            ->first();

        if ($existing && $existing->type === $data['type']) {
            $existing->delete();
        } elseif ($existing) {
            $existing->update(['type' => $data['type']]);
        } else {
            Reaction::create([
                'user_id' => $request->user()->id,
                'content_id' => $content->id,
                'type' => $data['type'],
            ]);
        }

        return back();
    }
}
