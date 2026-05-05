<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $q = trim((string) ($validated['q'] ?? ''));
        $spaces = collect();
        $people = collect();
        $hasPostHits = false;

        if ($q === '') {
            return view('search.index', [
                'q' => $q,
                'spaces' => $spaces,
                'people' => $people,
                'hasPostHits' => $hasPostHits,
            ]);
        }

        $term = '%'.addcslashes($q, '%_\\').'%';

        $hasPostHits = Publication::query()
            ->where('contents.status', 'visible')
            ->where(function ($query) use ($term) {
                $query->where('publications.title', 'like', $term)
                    ->orWhere('publications.text', 'like', $term);
            })
            ->exists();

        $spaces = Category::query()
            ->where('is_active', true)
            ->where('name', 'like', $term)
            ->orderBy('name')
            ->limit(25)
            ->get();

        $people = User::query()
            ->where('status', 'active')
            ->whereHas('profile', function ($profile) use ($term) {
                $profile->where('display_name', 'like', $term);
            })
            ->with('profile')
            ->orderBy('created_at', 'desc')
            ->limit(25)
            ->get();

        return view('search.index', [
            'q' => $q,
            'spaces' => $spaces,
            'people' => $people,
            'hasPostHits' => $hasPostHits,
        ]);
    }
}
