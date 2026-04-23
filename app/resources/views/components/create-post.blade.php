<?php

use App\Models\Content;
use App\Models\MediaAttachment;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $title = '';
    public string $text = '';
    public string $context = 'feed';
    public ?string $categoryId = null;
    public ?string $selectedCategoryId = null;
    public bool $expanded = false;
    public array $images = [];
    public bool $isPoll = false;
    public array $pollOptions = ['', ''];

    public function expand(): void
    {
        $this->expanded = true;
    }

    public function collapse(): void
    {
        $this->expanded = false;
    }

    public function updatedImages(): void
    {
        if (!empty($this->images)) {
            $this->expanded = true;
        }
    }

    public function startPoll(): void
    {
        $this->expanded = true;
        $this->isPoll = true;
        $this->images = [];

        if (count($this->pollOptions) < 2) {
            $this->pollOptions = ['', ''];
        }
    }

    public function removePoll(): void
    {
        $this->isPoll = false;
        $this->pollOptions = ['', ''];
    }

    public function addPollOption(): void
    {
        if (count($this->pollOptions) >= 6) {
            return;
        }

        $this->pollOptions[] = '';
    }

    public function removePollOption(int $index): void
    {
        if (count($this->pollOptions) <= 2 || !isset($this->pollOptions[$index])) {
            return;
        }

        unset($this->pollOptions[$index]);
        $this->pollOptions = array_values($this->pollOptions);
    }

    public function removeImage(int $index): void
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
    }

    public function mount(): void
    {
        if ($this->context === 'category' && $this->categoryId) {
            $this->selectedCategoryId = $this->categoryId;
        }
    }

    public function getFollowedCategoriesProperty()
    {
        return auth()->user()
            ->followedCategories()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['categories.id', 'categories.name']);
    }

    public function createPost(): void
    {
        $effectiveCategoryId = $this->context === 'category' && $this->categoryId
            ? $this->categoryId
            : $this->selectedCategoryId;

        $data = $this->validate([
            'title' => 'required|string|max:255',
            'text' => 'required_unless:isPoll,true|nullable|string',
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|max:5120',
            'selectedCategoryId' => 'nullable|uuid|exists:categories,id',
            'isPoll' => 'boolean',
            'pollOptions' => 'required_if:isPoll,true|array|min:2|max:6',
            'pollOptions.*' => 'required_if:isPoll,true|string|max:100',
        ]);

        if (! $effectiveCategoryId) {
            $this->addError('selectedCategoryId', 'Please select a category.');
            return;
        }

        $isFollowingCategory = auth()->user()
            ->followedCategories()
            ->where('categories.id', $effectiveCategoryId)
            ->exists();

        if (! $isFollowingCategory) {
            $this->addError('selectedCategoryId', 'You can only post in categories you follow.');
            return;
        }

        if ($this->isPoll && !empty($data['images'])) {
            $this->addError('images', 'A poll post cannot include images.');
            return;
        }

        $pollOptions = collect($data['pollOptions'] ?? [])
            ->map(fn ($option) => trim((string) $option))
            ->filter()
            ->values();

        if ($this->isPoll && $pollOptions->count() < 2) {
            $this->addError('pollOptions', 'Please provide at least 2 poll options.');
            return;
        }

        DB::transaction(function () use ($data, $effectiveCategoryId, $pollOptions): void {
            $content = Content::create([
                'type' => 'publication',
                'status' => 'visible',
                'author_id' => auth()->id(),
            ]);

            $mediaType = $this->isPoll
                ? 'poll'
                : (!empty($data['images']) ? 'image' : null);

            DB::table('publications')->insert([
                'id' => $content->id,
                'title' => $data['title'],
                'text' => $this->isPoll ? '' : ($data['text'] ?? ''),
                'media_type' => $mediaType,
                'category_id' => $effectiveCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach (($data['images'] ?? []) as $image) {
                $path = $image->store('publications', 'public');

                MediaAttachment::create([
                    'type' => 'image',
                    'url' => Storage::url($path),
                    'publication_id' => $content->id,
                ]);
            }

            if ($this->isPoll) {
                $poll = Poll::create([
                    'publication_id' => $content->id,
                    'question' => trim($data['title']),
                ]);

                foreach ($pollOptions as $label) {
                    PollOption::create([
                        'poll_id' => $poll->id,
                        'label' => $label,
                    ]);
                }
            }
        });

        $this->reset(['title', 'text', 'images', 'isPoll']);
        $this->pollOptions = ['', ''];
        $this->expanded = false;
        session()->flash('success', 'Publication posted.');

        if ($this->context === 'profile') {
            $this->redirectRoute('profile.edit');
            return;
        }

        if ($this->context === 'category' && $this->categoryId) {
            $this->redirectRoute('groups.show', $this->categoryId);
            return;
        }

        $this->redirectRoute('feed.index');
    }
};
?>

<div>
    <div class="card w-100 shadow-xss rounded-xxl border-0 ps-4 pt-3 pe-4 pb-3 mb-3 mt-3">
        <input type="file" id="create-post-image-input" class="d-none" wire:model="images" accept="image/*" multiple>

        <div class="card-body p-0 d-flex align-items-start">
            <figure class="avatar me-3 mb-0"><img src="{{ asset('images/profile-4.png') }}" alt="image" class="shadow-sm rounded-circle w45"></figure>
            <div class="flex-grow-1">
                @if(!$expanded)
                <div class="d-flex align-items-center" wire:transition.opacity.duration.300ms>
                    <button type="button" wire:click="expand"
                        class="flex-grow-1 text-start bor-0 rounded-xxl p-2 ps-4 font-xssss text-grey-500 fw-500 border-light-md theme-dark-bg bg-transparent"
                        style="cursor:pointer;">
                        What's on your mind, {{ auth()->user()->profile->display_name ?? 'there' }}?
                    </button>
                    <button
                        type="button"
                        onclick="var input = document.getElementById('create-post-image-input'); if (input) input.click();"
                        class="d-flex align-items-center ms-3 text-grey-600 border-0 bg-transparent"
                    >
                        <i class="feather-image font-md text-success me-1"></i>
                    </button>
                    <button
                        type="button"
                        wire:click="startPoll"
                        class="d-flex align-items-center ms-2 text-grey-600 border-0 bg-transparent"
                        title="Start a poll"
                    >
                        <i class="feather-bar-chart-2 font-md text-primary"></i>
                    </button>
                    <button type="button" wire:click="expand" class="d-flex align-items-center ms-2 text-grey-600 border-0 bg-transparent"><i class="feather-smile font-md text-warning"></i></button>
                </div>
                @endif

                @if($expanded)
                <div class="pt-1" wire:transition.opacity.duration.300ms>
                    <form wire:submit="createPost" id="createPostForm">
                        @if($context !== 'category' || !$categoryId)
                            <div
                                class="create-post-category-picker mb-2"
                                x-data="{
                                    open: false,
                                    query: '',
                                    selected: @entangle('selectedCategoryId').live,
                                    categories: @js($this->followedCategories->map(fn ($category) => ['id' => $category->id, 'name' => $category->name])->values()),
                                    get filtered() {
                                        const q = this.query.trim().toLowerCase();
                                        if (!q) return this.categories;
                                        return this.categories.filter(c => c.name.toLowerCase().includes(q));
                                    },
                                    get selectedLabel() {
                                        const match = this.categories.find(c => c.id === this.selected);
                                        return match ? match.name : 'Select category';
                                    },
                                    openPicker() {
                                        this.open = true;
                                        this.$nextTick(() => this.$refs.searchInput && this.$refs.searchInput.focus());
                                    },
                                    choose(id) {
                                        this.selected = id;
                                        this.open = false;
                                    }
                                }"
                                @click.outside="open = false"
                            >
                                <button type="button" class="create-post-category-trigger" @click="openPicker()">
                                    <i class="feather-search font-xss text-grey-500 me-1"></i>
                                    <span x-text="selectedLabel"></span>
                                </button>

                                <div class="create-post-category-dropdown" x-show="open" x-transition.opacity.duration.150ms>
                                    <div class="create-post-category-search-wrap">
                                        <i class="feather-search text-grey-500 font-xss"></i>
                                        <input
                                            x-ref="searchInput"
                                            type="text"
                                            class="create-post-category-search"
                                            placeholder="Search category..."
                                            x-model="query"
                                        >
                                    </div>
                                    <div class="create-post-category-list">
                                        <template x-for="item in filtered" :key="item.id">
                                            <button type="button" class="create-post-category-item" @click="choose(item.id)">
                                                <span x-text="item.name"></span>
                                            </button>
                                        </template>
                                        <div class="create-post-category-empty" x-show="filtered.length === 0">No matching category</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <input type="text" wire:model="title" class="bor-0 w-100 rounded-xxl p-2 ps-3 font-xssss text-grey-500 fw-500 border-light-md theme-dark-bg mb-2" placeholder="Post title" required>
                        @error('title')
                            <div class="text-danger font-xssss mb-2">{{ $message }}</div>
                        @enderror

                        @if($context === 'category' && $categoryId)
                            <input type="hidden" wire:model="selectedCategoryId">
                        @endif

                        @if(!$isPoll)
                            <div
                                x-data="createPostQuillEditor(@entangle('text').live)"
                                x-init="init()"
                                class="create-post-editor-wrap"
                            >
                                <div wire:ignore>
                                    <div x-ref="editor" class="create-post-quill"></div>
                                </div>
                            </div>
                            @error('text')
                                <div class="text-danger font-xssss mt-2">{{ $message }}</div>
                            @enderror
                        @endif

                        @if($isPoll)
                            <div class="create-post-poll-box mt-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h4 class="fw-700 text-grey-900 font-xssss mb-0">Poll</h4>
                                    <button type="button" wire:click="removePoll" class="border-0 bg-transparent text-danger font-xssss fw-600 p-0">Remove poll</button>
                                </div>
                                <div class="font-xssss text-grey-500 mb-2">Poll question will use the post title.</div>

                                @foreach($pollOptions as $index => $option)
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <input
                                            type="text"
                                            wire:model="pollOptions.{{ $index }}"
                                            class="bor-0 w-100 rounded-xxl p-2 ps-3 font-xssss text-grey-700 fw-500 border-light-md theme-dark-bg"
                                            placeholder="Option {{ $index + 1 }}"
                                        >
                                        @if(count($pollOptions) > 2)
                                            <button type="button" wire:click="removePollOption({{ $index }})" class="border-0 bg-transparent text-danger p-0">
                                                <i class="feather-x"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                @error('pollOptions')
                                    <div class="text-danger font-xssss mb-2">{{ $message }}</div>
                                @enderror
                                @error('pollOptions.*')
                                    <div class="text-danger font-xssss mb-2">{{ $message }}</div>
                                @enderror

                                <button type="button" wire:click="addPollOption" class="border-0 bg-transparent text-primary font-xssss fw-600 p-0">+ Add option</button>
                            </div>
                        @endif

                        @if(!empty($images))
                            <div class="mt-3 d-flex flex-wrap gap-2">
                                @foreach($images as $index => $image)
                                    <div class="position-relative">
                                        <div class="card border-0 shadow-sm rounded-3 overflow-hidden" style="width: 140px;">
                                            <img src="{{ $image->temporaryUrl() }}" alt="selected image" class="w-100" style="height: 100px; object-fit: cover;">
                                        </div>
                                        <button
                                            type="button"
                                            wire:click="removeImage({{ $index }})"
                                            class="position-absolute top-0 end-0 mt-1 me-1 border-0 rounded-circle d-inline-flex align-items-center justify-content-center bg-dark text-white"
                                            style="width: 22px; height: 22px;"
                                            title="Remove image"
                                        >
                                            <i class="feather-x font-xss"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="d-flex align-items-center gap-2 mt-3">
                            @if(!$isPoll)
                                <button type="button" onclick="document.getElementById('create-post-image-input').click()" class="d-flex align-items-center text-grey-600 border-0 bg-transparent p-0" title="Add image">
                                    <i class="feather-image font-md text-success me-1"></i>
                                </button>
                            @endif
                            <button type="button" wire:click="startPoll" class="d-flex align-items-center text-grey-600 border-0 bg-transparent p-0" title="Start a poll">
                                <i class="feather-bar-chart-2 font-md text-primary me-1"></i>
                                @if($isPoll)
                                    <span class="font-xssss fw-600 text-primary">Poll mode</span>
                                @endif
                            </button>
                            @if(!empty($images))<span class="font-xssss text-grey-500">{{ count($images) }} image(s) selected</span>@endif
                            @error('images')
                                <span class="text-danger font-xssss">{{ $message }}</span>
                            @enderror
                            @error('images.*')
                                <span class="text-danger font-xssss">{{ $message }}</span>
                            @enderror
                            <div class="ms-auto d-flex align-items-center gap-2">
                                <button type="submit" class="border-0 bg-primary-gradiant text-white text-center font-xssss fw-700 rounded-3 cursor-pointer d-inline-flex align-items-center justify-content-center px-3" style="height:48px; min-width:78px;">
                                    Post
                                </button>
                                <button type="button" wire:click="collapse" class="create-post-cancel-btn border-0 bg-greylight text-grey-700 text-center font-xssss fw-700 rounded-3 cursor-pointer d-inline-flex align-items-center justify-content-center px-3" style="height:48px; min-width:78px;">
                                    Cancel
                                </button>
                            </div>
                        </div>
                        @if($context !== 'category' || !$categoryId)
                            @error('selectedCategoryId')
                                <div class="text-danger font-xssss mt-2">{{ $message }}</div>
                            @enderror
                        @endif
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
@once
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        var createPostQuillSizeRegistered = false;

        function createPostQuillEditor(textModel) {
            return {
                text: textModel ?? '',
                quill: null,
                syncingFromLivewire: false,
                init() {
                    if (typeof Quill === 'undefined') {
                        return;
                    }
                    if (!this.$refs.editor || this.$refs.editor.dataset.quillInitialized === '1') {
                        return;
                    }

                    this.$refs.editor.dataset.quillInitialized = '1';

                    if (!createPostQuillSizeRegistered) {
                        const Size = Quill.import('attributors/style/size');
                        Size.whitelist = ['20px'];
                        Quill.register(Size, true);
                        createPostQuillSizeRegistered = true;
                    }

                    this.quill = new Quill(this.$refs.editor, {
                        theme: 'snow',
                        placeholder: "What's on your mind?",
                        modules: {
                            toolbar: {
                                container: [
                                    ['subheadline'],
                                    ['bold', 'italic', 'underline'],
                                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                    ['link'],
                                    ['clean']
                                ],
                                handlers: {
                                    subheadline: function () {
                                        const range = this.quill.getSelection();
                                        const currentFormat = range
                                            ? this.quill.getFormat(range)
                                            : this.quill.getFormat();
                                        const nextSize = currentFormat.size === '20px' ? false : '20px';
                                        this.quill.format('size', nextSize);
                                    }
                                }
                            }
                        }
                    });

                    if (this.text) {
                        this.quill.root.innerHTML = this.text;
                    }

                    this.quill.on('text-change', () => {
                        if (!this.quill) {
                            return;
                        }

                        const plainText = this.quill.getText().trim();
                        const html = plainText.length ? this.quill.root.innerHTML : '';
                        this.syncingFromLivewire = true;
                        this.text = html;
                        this.$nextTick(() => {
                            this.syncingFromLivewire = false;
                        });
                    });

                    this.$watch('text', (value) => {
                        if (!this.quill || this.syncingFromLivewire) {
                            return;
                        }

                        const current = this.quill.root.innerHTML;
                        const nextValue = value || '';
                        if (current !== nextValue) {
                            this.quill.root.innerHTML = nextValue;
                        }
                    });
                }
            };
        }
    </script>
@endonce

<style>
    .create-post-editor-wrap .ql-toolbar.ql-snow {
        border: 1px solid #d7dde5;
        border-bottom: 0;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
        display: flex;
        align-items: center;
        gap: 2px;
    }

    .create-post-editor-wrap .ql-container.ql-snow {
        border: 1px solid #d7dde5;
        border-bottom-left-radius: 14px;
        border-bottom-right-radius: 14px;
        min-height: 140px;
        background: #f7f7f7;
    }

    .create-post-editor-wrap .ql-editor {
        min-height: 120px;
        font-size: 14px;
        color: #495057;
    }

    .create-post-editor-wrap .ql-snow .ql-toolbar button.ql-subheadline,
    .create-post-editor-wrap .ql-snow.ql-toolbar button.ql-subheadline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 24px;
        padding: 0;
        position: relative;
    }

    .create-post-editor-wrap .ql-snow .ql-toolbar button.ql-subheadline::before,
    .create-post-editor-wrap .ql-snow.ql-toolbar button.ql-subheadline::before {
        content: "H";
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        line-height: 1;
    }

    .create-post-cancel-btn {
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .create-post-cancel-btn:hover {
        background-color: #fde7e9 !important;
        color: #dc3545 !important;
    }

    .create-post-category-picker {
        position: relative;
    }

    .create-post-category-trigger {
        height: 34px;
        min-width: 210px;
        border: 1px solid #d7dde5;
        border-radius: 999px;
        background: #f5f7fa;
        color: #495057;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        padding: 0 12px;
    }

    .create-post-category-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        width: 280px;
        max-height: 260px;
        z-index: 10000;
        background: #fff;
        border: 1px solid #d7dde5;
        border-radius: 12px;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }

    .create-post-category-search-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid #eef1f4;
        padding: 10px 12px;
    }

    .create-post-category-search {
        border: 0;
        outline: 0;
        width: 100%;
        font-size: 12px;
        color: #495057;
        background: transparent;
    }

    .create-post-category-list {
        max-height: 200px;
        overflow-y: auto;
        padding: 6px;
    }

    .create-post-category-item {
        width: 100%;
        text-align: left;
        border: 0;
        background: transparent;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        color: #343a40;
        padding: 8px 10px;
    }

    .create-post-category-item:hover {
        background: #f1f3f5;
    }

    .create-post-category-empty {
        font-size: 12px;
        color: #868e96;
        padding: 10px;
    }

    .create-post-poll-box {
        border: 1px solid #d7dde5;
        border-radius: 12px;
        background: #fafcff;
        padding: 12px;
    }
</style>
</div>
