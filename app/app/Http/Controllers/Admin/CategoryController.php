<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::latest()->get();

        return view('admin.categories', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255|unique:categories,name']);
        Category::create($data);

        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255|unique:categories,name,'.$category->id]);
        $category->update($data);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}
