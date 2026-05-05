<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class CategoryGroupsList extends Component
{
    public int $initialLoad = 10;

    public int $loadStep = 4;

    public int $loadedCount = 10;

    public bool $hasMore = true;

    public bool $isLoadingMore = false;

    public array $followedCategoryIds = [];

    /** When true, only spaces the authenticated user follows (sidebar Spaces). */
    public bool $followingOnly = false;

    public function mount(): void
    {
        if (! auth()->check()) {
            $this->followedCategoryIds = [];

            return;
        }

        $this->followedCategoryIds = auth()->user()
            ->followedCategories()
            ->pluck('categories.id')
            ->all();
    }

    public function loadMore(): void
    {
        if (!$this->hasMore || $this->isLoadingMore) {
            return;
        }

        $this->isLoadingMore = true;
        $this->loadedCount += $this->loadStep;
        $this->isLoadingMore = false;
    }

    public function follow(string $categoryId): void
    {
        if (! auth()->check()) {
            return;
        }

        auth()->user()
            ->followedCategories()
            ->syncWithoutDetaching([$categoryId]);

        if (!in_array($categoryId, $this->followedCategoryIds, true)) {
            $this->followedCategoryIds[] = $categoryId;
        }
    }

    public function render()
    {
        $baseQuery = Category::query()
            ->where('is_active', true)
            ->withCount('followers');

        if ($this->followingOnly) {
            $baseQuery->whereIn('id', $this->followedCategoryIds)->orderBy('name');
        } else {
            $baseQuery->latest();
        }

        $total = (clone $baseQuery)->count();
        if ($this->loadedCount < $this->initialLoad) {
            $this->loadedCount = $this->initialLoad;
        }
        $spaces = $baseQuery
            ->take($this->loadedCount)
            ->get();

        $this->hasMore = $spaces->count() < $total;

        return view('livewire.category-groups-list', [
            'spaces' => $spaces,
            'followingOnly' => $this->followingOnly,
        ]);
    }
}

