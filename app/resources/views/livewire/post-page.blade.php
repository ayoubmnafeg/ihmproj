<div>
    <div class="row feed-body">
        <div class="col-xl-11 col-lg-11 mx-auto">
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
                </div>

                <div class="card-body p-0 me-lg-5 mt-2">
                    <h5 class="post-title-headline mb-1">{{ $publication->title }}</h5>
                    <div class="post-content fw-500 text-grey-500 lh-26 font-xssss w-100">{!! $publication->text !!}</div>

                    <x-post.photos-grid :attachments="$publication->attachments" />
                </div>

            <x-post.reaction-bar
                :content="$publication"
                :comments-count="$publication->comments_count"
                :share-url="route('publications.show', $publication->id)"
            />

                <div id="comments-{{ $publication->id }}" class="card-body p-0 mt-3">
                    <x-post.comment-form
                        :publication-id="$publication->id"
                        placeholder="Add a comment..."
                        wire-submit="addComment"
                        wire-model="newComment"
                        wrapper-class="mb-3"
                    />

                    @foreach($publication->comments->where('parent_id', null) as $comment)
                        <x-post.comment-item
                            :comment="$comment"
                            :all-comments="$publication->comments"
                            :publication-id="$publication->id"
                            :open-reply-forms="$openReplyForms"
                        />
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if($pendingDeleteCommentId)
        <div class="app-modal-backdrop"></div>
        <div class="app-modal-wrap" role="dialog" aria-modal="true" aria-labelledby="deleteCommentModalTitle">
            <div class="app-modal-card shadow-xss rounded-xxl border-0">
                <h4 id="deleteCommentModalTitle" class="fw-700 text-grey-900 font-xsss mb-2">Delete comment?</h4>
                <p class="fw-500 text-grey-500 font-xssss mb-4">This action cannot be undone. If the comment has replies, its content will be replaced with "content is deleted".</p>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" wire:click="cancelDeleteComment" class="app-modal-btn app-modal-btn-cancel">Cancel</button>
                    <button type="button" wire:click="confirmDeleteComment" class="app-modal-btn app-modal-btn-danger">Delete</button>
                </div>
            </div>
        </div>
    @endif

    <style>
    .comment-actions-row {
        flex-wrap: wrap;
    }

    .post-title-headline {
        color: #111;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
    }

    .post-content p {
        margin-top: 0;
        margin-bottom: 0.4rem;
    }

    .post-content p:last-child {
        margin-bottom: 0;
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

    .comment-action-btn.active-upvote {
        color: #16a34a;
    }

    .comment-action-btn.active-downvote {
        color: #dc2626;
    }

    .comment-input-wrap {
        position: relative;
    }

    .comment-send-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border: 0;
        background: #d1d5db;
        color: #ffffff;
        cursor: pointer;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s ease;
        line-height: 1;
    }

    .comment-input-wrap input:not(:placeholder-shown) + .comment-send-btn {
        background: #0d6efd;
    }

    .post-photos-grid {
        display: grid;
        gap: 8px;
    }

    .app-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(19, 25, 40, 0.48);
        z-index: 2000;
    }

    .app-modal-wrap {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2001;
        padding: 16px;
    }

    .app-modal-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        padding: 20px;
    }

    .app-modal-btn {
        border: 0;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 14px;
        cursor: pointer;
    }

    .app-modal-btn-cancel {
        background: #f1f3f5;
        color: #495057;
    }

    .app-modal-btn-danger {
        background: #e03131;
        color: #fff;
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

    .post-photos-grid.photos-count-4,
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

    <script>
        (function () {
            document.addEventListener('click', function (event) {
                var shareButton = event.target.closest('.copy-comment-link, .copy-post-link');
                if (!shareButton) return;

                var commentLink = shareButton.getAttribute('data-link');
                if (!commentLink) return;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(commentLink);
                }
            });
        })();
    </script>
</div>
