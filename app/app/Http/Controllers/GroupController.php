<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(): View
    {
        return view('groups.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:categories,name',
            'description' => 'nullable|string|max:2000',
            'profile_image' => 'nullable|image|max:4096',
        ]);

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('categories', 'public');
        }

        Category::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'profile_image_path' => $profileImagePath,
            'is_active' => true,
        ]);

        return redirect()->route('groups.index')->with('success', 'Category created successfully.');
    }

    public function follow(Request $request, Category $category): RedirectResponse
    {
        $alreadyFollowing = $request->user()
            ->followedCategories()
            ->where('categories.id', $category->id)
            ->exists();

        if ($alreadyFollowing) {
            $request->user()
                ->followedCategories()
                ->detach($category->id);

            return back()->with('success', 'Category unfollowed successfully.');
        }

        $request->user()
            ->followedCategories()
            ->syncWithoutDetaching([$category->id]);

        return back()->with('success', 'Category followed successfully.');
    }

    public function show(Category $category): View
    {
        $category->loadCount('followers', 'publications');

        $isFollowing = auth()->user()
            ->followedCategories()
            ->where('categories.id', $category->id)
            ->exists();

        return view('groups.show', [
            'group' => $category,
            'isFollowing' => $isFollowing,
        ]);
    }
}

