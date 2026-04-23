@props([
    'comment',
    'allComments',
    'publicationId',
    'openReplyForms' => [],
    'depth' => 0,
])

@php
    $hasVisibleDescendant = function (string $commentId) use ($allComments, &$hasVisibleDescendant): bool {
        $children = $allComments->where('parent_id', $commentId);

        foreach ($children as $child) {
            if ($child->status !== 'deleted' || $hasVisibleDescendant($child->id)) {
                return true;
            }
        }

        return false;
    };

    $childComments = $allComments
        ->where('parent_id', $comment->id)
        ->filter(fn ($child) => $child->status !== 'deleted' || $hasVisibleDescendant($child->id))
        ->sortByDesc('created_at');

    $shouldRender = $comment->status !== 'deleted' || $childComments->count() > 0;
    $paddingLeft = min($depth * 28, 112);
@endphp

@if($shouldRender)
<div id="comment-{{ $comment->id }}" class="comment-thread-item mb-3 pb-2" style="padding-left: {{ $paddingLeft }}px;">
    <div class="d-flex align-items-start w-100">
        <figure class="avatar me-2 mb-0"><img src="{{ asset('images/profile-4.png') }}" alt="image" class="shadow-sm rounded-circle w30"></figure>
        <div class="flex-fill">
            <div class="d-flex align-items-center">
                <h5 class="fw-700 text-grey-900 font-xssss mb-1">{{ $comment->author->profile->display_name ?? 'Unknown' }}</h5>
                @if($comment->status !== 'deleted' && (auth()->id() === $comment->author_id || auth()->user()->isAdmin()))
                    <button
                        type="button"
                        class="comment-action-btn text-danger ms-2 mb-1"
                        wire:click="promptDeleteComment('{{ $comment->id }}')"
                    >
                        <i class="feather-trash-2"></i> Delete
                    </button>
                @endif
            </div>
            @if($comment->status === 'deleted')
                <p class="fw-500 text-grey-400 font-xssss mb-1 lh-24 fst-italic">content is deleted</p>
            @else
                <p class="fw-500 text-grey-500 font-xssss mb-1 lh-24">{{ $comment->text }}</p>
            @endif
            <div class="d-flex align-items-center gap-2 comment-actions-row">
                @if($comment->status !== 'deleted')
                    <form method="POST" action="{{ route('reactions.toggle', $comment->id) }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="type" value="upvote">
                        <button type="submit" class="comment-action-btn {{ ($comment->reactions->first()?->type) === 'upvote' ? 'active-upvote' : '' }}">
                            <i class="feather-arrow-up"></i> {{ $comment->upvotes_count ?? 0 }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('reactions.toggle', $comment->id) }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="type" value="downvote">
                        <button type="submit" class="comment-action-btn {{ ($comment->reactions->first()?->type) === 'downvote' ? 'active-downvote' : '' }}">
                            <i class="feather-arrow-down"></i> {{ $comment->downvotes_count ?? 0 }}
                        </button>
                    </form>
                    <button type="button" class="comment-action-btn copy-comment-link" data-link="{{ route('publications.show', $publicationId) }}#comment-{{ $comment->id }}">
                        <i class="feather-share-2"></i> Share
                    </button>
                    <button type="button" class="comment-action-btn" wire:click="toggleReplyForm('{{ $comment->id }}')">
                        <i class="feather-corner-down-right"></i> Reply
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if($childComments->count())
        <div class="mt-2">
            @foreach($childComments as $reply)
                <x-post.comment-item
                    :comment="$reply"
                    :all-comments="$allComments"
                    :publication-id="$publicationId"
                    :open-reply-forms="$openReplyForms"
                    :depth="$depth + 1"
                />
            @endforeach
        </div>
    @endif

    <x-post.comment-form
        :publication-id="$publicationId"
        form-id="reply-form-{{ $comment->id }}"
        placeholder="Reply to this comment..."
        wire-submit="addReply('{{ $comment->id }}')"
        wire-model="replyDrafts.{{ $comment->id }}"
        :hidden="$comment->status === 'deleted' || !($openReplyForms[$comment->id] ?? false)"
        wrapper-class="mt-2"
    />
</div>
@endif
