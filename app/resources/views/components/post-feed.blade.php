<?php

use App\Models\Publication;
use Livewire\Component;

new class extends Component
{
    public string $scope = 'all';
    public ?string $userId = null;
    public ?string $categoryId = null;
    public int $perPage = 20;
    public bool $hasMore = true;

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->perPage += 20;
    }

    public function getPublicationsProperty()
    {
        $query = Publication::with(['author.profile', 'category', 'attachments'])
            ->with([
                'reactions' => fn ($reactionQuery) => $reactionQuery->where('user_id', auth()->id()),
            ])
            ->withCount('comments')
            ->withCount([
                'reactions as upvotes_count' => fn ($reactionQuery) => $reactionQuery->where('type', 'upvote'),
                'reactions as downvotes_count' => fn ($reactionQuery) => $reactionQuery->where('type', 'downvote'),
            ])
            ->where('contents.status', 'visible')
            ->latest('contents.created_at');

        if ($this->scope === 'mine') {
            $query->where('contents.author_id', auth()->id());
        }

        if ($this->scope === 'user' && $this->userId) {
            $query->where('contents.author_id', $this->userId);
        }

        if ($this->categoryId) {
            $query->where('publications.category_id', $this->categoryId);
        }

        $publications = $query->take($this->perPage + 1)->get();
        $this->hasMore = $publications->count() > $this->perPage;

        return $publications->take($this->perPage);
    }

    public function render()
    {
        return view('components.post-feed', [
            'publications' => $this->publications,
        ]);
    }
};
?>

<div>
    @forelse($publications as $publication)
    @php
        $userReactionType = $publication->reactions->first()?->type;
        $photoCount = $publication->attachments->count();
        $displayAttachments = $photoCount >= 4 ? $publication->attachments->take(4) : $publication->attachments;
        $remainingPhotoCount = max($photoCount - 4, 0);
        $photoLayoutVariant = $photoCount >= 4 ? '4plus' : (string) $photoCount;
    @endphp
    <div class="card w-100 shadow-xss rounded-xxl border-0 p-4 mb-3">
        <div class="card-body p-0 d-flex">
            <figure class="avatar me-3"><img src="{{ asset('images/profile-4.png') }}" alt="image" class="shadow-sm rounded-circle w45"></figure>
            <h4 class="fw-700 text-grey-900 font-xssss mt-1">
                <a href="{{ route('profile.show', $publication->author_id) }}" class="text-grey-900">{{ $publication->author->profile->display_name ?? 'Unknown' }}</a>
                <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">
                    {{ $publication->created_at->diffForHumans() }}
                    @if($publication->category) &middot; {{ $publication->category->name }} @endif
                </span>
            </h4>
            <a href="#" class="ms-auto" id="dropdownPub{{ $publication->id }}" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti-more-alt text-grey-900 btn-round-md bg-greylight font-xss"></i></a>
            <div class="dropdown-menu dropdown-menu-end p-4 rounded-xxl border-0 shadow-lg" aria-labelledby="dropdownPub{{ $publication->id }}">
                <div class="card-body p-0 d-flex">
                    <i class="feather-bookmark text-grey-500 me-3 font-lg"></i>
                    <h4 class="fw-600 text-grey-900 font-xssss mt-0 me-4">Save Link <span class="d-block font-xsssss fw-500 mt-1 lh-3 text-grey-500">Add this to your saved items</span></h4>
                </div>
                <div class="card-body p-0 d-flex mt-2">
                    <i class="feather-alert-circle text-grey-500 me-3 font-lg"></i>
                    <h4 class="fw-600 text-grey-900 font-xssss mt-0 me-4">Hide Post <span class="d-block font-xsssss fw-500 mt-1 lh-3 text-grey-500">Save to your saved items</span></h4>
                </div>
                @if(auth()->id() === $publication->author_id || auth()->user()->isAdmin())
                <div class="card-body p-0 d-flex mt-2">
                    <i class="feather-trash text-grey-500 me-3 font-lg"></i>
                    <form method="POST" action="{{ route('publications.destroy', $publication->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="border-0 bg-transparent p-0 fw-600 text-grey-900 font-xssss mt-0 me-4 cursor-pointer">
                            Delete Post <span class="d-block font-xsssss fw-500 mt-1 lh-3 text-grey-500">Remove this post</span>
                        </button>
                    </form>
                </div>
                @endif
                <div class="card-body p-0 d-flex mt-2">
                    <i class="feather-flag text-grey-500 me-3 font-lg"></i>
                    <form method="POST" action="{{ route('reports.store', $publication->id) }}">
                        @csrf
                        <input type="hidden" name="reason" value="inappropriate">
                        <button type="submit" class="border-0 bg-transparent p-0 fw-600 text-grey-900 font-xssss mt-0 me-4 cursor-pointer">
                            Report Post <span class="d-block font-xsssss fw-500 mt-1 lh-3 text-grey-500">Flag as inappropriate</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0 me-lg-5 mt-2">
            <h5 class="post-title-headline mb-1">{{ $publication->title }}</h5>
            <div
                x-data="{
                    expanded: false,
                    showToggle: false,
                    checkOverflow() {
                        this.$nextTick(() => {
                            if (!this.$refs.content) return;
                            if (this.expanded) {
                                this.showToggle = true;
                                return;
                            }

                            this.showToggle = this.$refs.content.scrollHeight > this.$refs.content.clientHeight + 2;
                        });
                    }
                }"
                x-init="checkOverflow()"
            >
                <div
                    x-ref="content"
                    class="post-content fw-500 text-grey-500 lh-26 font-xssss w-100"
                    :class="{ 'post-content--collapsed': !expanded }"
                >{!! $publication->text !!}</div>

                <button
                    type="button"
                    class="see-more-btn mt-1"
                    x-show="showToggle || expanded"
                    x-on:click="expanded = !expanded; $nextTick(() => checkOverflow())"
                    x-text="expanded ? 'See less' : 'See more'"
                ></button>
            </div>
            @if($photoCount > 0)
                <div class="d-flex align-items-center mt-2 mb-2">
                    <i class="feather-image text-grey-500 me-2 font-xss"></i>
                    <span class="fw-600 text-grey-700 font-xssss">{{ $photoCount }} {{ $photoCount === 1 ? 'Photo' : 'Photos' }}</span>
                </div>
            @endif
            @if($publication->attachments->count())
                <div class="mt-2 post-photos-grid photos-count-{{ $photoLayoutVariant }}">
                    @foreach($displayAttachments as $index => $attachment)
                        @php $isLastShownWithOverlay = $remainingPhotoCount > 0 && $index === 3; @endphp
                        <div class="post-photo-item {{ $isLastShownWithOverlay ? 'has-more-overlay' : '' }}">
                            <img src="{{ $attachment->url }}" alt="attachment" class="w-100 h-100">
                            @if($isLastShownWithOverlay)
                                <span class="post-photo-more-count">+{{ $remainingPhotoCount }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card-body d-flex align-items-center p-0 mt-3">
            <form method="POST" action="{{ route('reactions.toggle', $publication->id) }}" class="d-inline me-2">
                @csrf
                <input type="hidden" name="type" value="upvote">
                <button type="submit" class="d-flex align-items-center fw-600 text-grey-900 lh-26 font-xssss border-0 bg-transparent p-0 cursor-pointer">
                    <i class="feather-arrow-up {{ $userReactionType === 'upvote' ? 'text-success' : 'text-grey-600' }} me-1 font-xss"></i>
                    <span>{{ $publication->upvotes_count }}</span>
                </button>
            </form>
            <form method="POST" action="{{ route('reactions.toggle', $publication->id) }}" class="d-inline me-3">
                @csrf
                <input type="hidden" name="type" value="downvote">
                <button type="submit" class="d-flex align-items-center fw-600 text-grey-900 lh-26 font-xssss border-0 bg-transparent p-0 cursor-pointer">
                    <i class="feather-arrow-down {{ $userReactionType === 'downvote' ? 'text-danger' : 'text-grey-600' }} me-1 font-xss"></i>
                    <span>{{ $publication->downvotes_count }}</span>
                </button>
            </form>
            <a href="{{ route('publications.show', $publication->id) }}" class="d-flex align-items-center fw-600 text-grey-900 lh-26 font-xssss">
                <i class="feather-message-circle text-grey-900 me-1 font-xss"></i>
                <span class="d-none-xss">{{ $publication->comments_count }} {{ $publication->comments_count === 1 ? 'Comment' : 'Comments' }}</span>
            </a>
            <button type="button" class="comment-action-btn ms-3 copy-post-link" data-link="{{ route('publications.show', $publication->id) }}">
                <i class="feather-share-2"></i> Share
            </button>
        </div>
    </div>
    @empty
    <div class="card w-100 shadow-xss rounded-xxl border-0 p-4 mb-3 text-center">
        <p class="fw-500 text-grey-500 font-xssss mb-0">No publications yet. Be the first to post!</p>
    </div>
    @endforelse

    @if($hasMore)
    <div wire:poll.visible.750ms="loadMore" class="card w-100 text-center shadow-xss rounded-xxl border-0 p-4 mb-3 mt-3">
        <div class="snippet mt-2 ms-auto me-auto" data-title=".dot-typing">
            <div class="stage">
                <div class="dot-typing"></div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    (function () {
        document.addEventListener('click', function (event) {
            var shareButton = event.target.closest('.copy-post-link');
            if (!shareButton) return;

            var postLink = shareButton.getAttribute('data-link');
            if (!postLink) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(postLink);
            }
        });
    })();
</script>

<style>
    .post-title-headline {
        color: #111;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
    }

    .post-content--collapsed {
        display: -webkit-box;
        -webkit-line-clamp: 5;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .post-content p {
        margin-top: 0;
        margin-bottom: 0.4rem;
    }

    .post-content p:last-child {
        margin-bottom: 0;
    }

    .see-more-btn {
        border: 0;
        background: transparent;
        padding: 0;
        color: #0d6efd;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }

    .post-content span[style*="font-size: 28px"],
    .post-content p[style*="font-size: 28px"] {
        color: #111 !important;
        font-weight: 700;
    }

    .post-content span[style*="font-size: 20px"],
    .post-content p[style*="font-size: 20px"] {
        color: #343a40 !important;
        font-weight: 600;
        margin-bottom: 0.2rem;
    }

    .post-content span[style*="font-size: 14px"],
    .post-content p[style*="font-size: 14px"] {
        color: #6c757d !important;
    }

    .comment-action-btn {
        border: 0;
        background: transparent;
        padding: 0;
        color: #6c757d;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .comment-action-btn:hover {
        color: #111;
    }
</style>

<style>
    .post-photos-grid {
        display: grid;
        gap: 8px;
    }

    .post-photo-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        height: 180px;
    }

    .post-photo-item img {
        object-fit: cover;
    }

    .post-photos-grid.photos-count-1 {
        grid-template-columns: 1fr;
    }

    .post-photos-grid.photos-count-2 {
        grid-template-columns: repeat(2, 1fr);
    }

    .post-photos-grid.photos-count-3 {
        grid-template-columns: repeat(3, 1fr);
    }

    .post-photos-grid.photos-count-4 {
        grid-template-columns: repeat(2, 1fr);
    }

    .post-photos-grid.photos-count-4plus {
        grid-template-columns: repeat(2, 1fr);
    }

    .post-photo-item.has-more-overlay::after {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
    }

    .post-photo-more-count {
        position: absolute;
        inset: 0;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 30px;
        letter-spacing: 0.5px;
    }

    @media (max-width: 575.98px) {
        .post-photos-grid.photos-count-3,
        .post-photos-grid.photos-count-4plus {
            grid-template-columns: repeat(2, 1fr);
        }

        .post-photos-grid.photos-count-3 .post-photo-item:nth-child(3),
        .post-photos-grid.photos-count-4plus .post-photo-item {
            grid-column: span 1;
            height: 160px;
        }
    }
</style>
