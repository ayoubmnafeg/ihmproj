<?php

use App\Models\Content;
use App\Models\MediaAttachment;
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
    public bool $expanded = false;
    public array $images = [];

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

    public function removeImage(int $index): void
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
    }

    public function createPost(): void
    {
        $data = $this->validate([
            'title' => 'required|string|max:255',
            'text' => 'required|string',
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|max:5120',
        ]);

        $content = Content::create([
            'type' => 'publication',
            'status' => 'visible',
            'author_id' => auth()->id(),
        ]);

        $mediaType = !empty($data['images']) ? 'image' : null;

        DB::table('publications')->insert([
            'id' => $content->id,
            'title' => $data['title'],
            'text' => $data['text'],
            'media_type' => $mediaType,
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

        $this->reset(['title', 'text', 'images']);
        $this->expanded = false;
        session()->flash('success', 'Publication posted.');

        if ($this->context === 'profile') {
            $this->redirectRoute('profile.edit');
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
                    <button type="button" wire:click="expand" class="d-flex align-items-center ms-2 text-grey-600 border-0 bg-transparent"><i class="feather-smile font-md text-warning"></i></button>
                </div>
                @endif

                @if($expanded)
                <div class="pt-1" wire:transition.opacity.duration.300ms>
                    <form wire:submit="createPost" id="createPostForm">
                        <input type="text" wire:model="title" class="bor-0 w-100 rounded-xxl p-2 ps-3 font-xssss text-grey-500 fw-500 border-light-md theme-dark-bg mb-2" placeholder="Post title" required>
                        @error('title')
                            <div class="text-danger font-xssss mb-2">{{ $message }}</div>
                        @enderror

                        <textarea wire:model="text" class="bor-0 w-100 rounded-xxl p-2 ps-3 font-xssss text-grey-500 fw-500 border-light-md theme-dark-bg" rows="5" placeholder="What's on your mind, {{ auth()->user()->profile->display_name ?? 'there' }}?" required></textarea>
                        @error('text')
                            <div class="text-danger font-xssss mt-2">{{ $message }}</div>
                        @enderror

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
                            <button type="button" onclick="document.getElementById('create-post-image-input').click()" class="d-flex align-items-center text-grey-600 border-0 bg-transparent p-0" title="Add image">
                                <i class="feather-image font-md text-success me-1"></i>
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
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .create-post-cancel-btn {
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .create-post-cancel-btn:hover {
        background-color: #fde7e9 !important;
        color: #dc3545 !important;
    }
</style>