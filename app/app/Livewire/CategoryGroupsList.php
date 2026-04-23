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

    public function mount(): void
    {
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
            ->withCount('followers')
            ->latest();

        $total = (clone $baseQuery)->count();
        if ($this->loadedCount < $this->initialLoad) {
            $this->loadedCount = $this->initialLoad;
        }
        $groups = $baseQuery
            ->take($this->loadedCount)
            ->get();

        $this->hasMore = $groups->count() < $total;

        return view('livewire.category-groups-list', [
            'groups' => $groups,
        ]);
    }
}

