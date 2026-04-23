@props(['content', 'commentsCount' => 0, 'shareUrl' => null])

@php
    $userReactionType = $content->reactions->first()?->type;
@endphp

<div class="card-body d-flex align-items-center p-0 mt-3">
    <form method="POST" action="{{ route('reactions.toggle', $content->id) }}" class="d-inline me-2">
        @csrf
        <input type="hidden" name="type" value="upvote">
        <button type="submit" class="d-flex align-items-center fw-600 text-grey-900 lh-26 font-xssss border-0 bg-transparent p-0 cursor-pointer">
            <i class="feather-arrow-up {{ $userReactionType === 'upvote' ? 'text-success' : 'text-grey-600' }} me-1 font-xss"></i>
            <span>{{ $content->upvotes_count ?? 0 }}</span>
        </button>
    </form>
    <form method="POST" action="{{ route('reactions.toggle', $content->id) }}" class="d-inline me-3">
        @csrf
        <input type="hidden" name="type" value="downvote">
        <button type="submit" class="d-flex align-items-center fw-600 text-grey-900 lh-26 font-xssss border-0 bg-transparent p-0 cursor-pointer">
            <i class="feather-arrow-down {{ $userReactionType === 'downvote' ? 'text-danger' : 'text-grey-600' }} me-1 font-xss"></i>
            <span>{{ $content->downvotes_count ?? 0 }}</span>
        </button>
    </form>
    <span class="d-flex align-items-center fw-600 text-grey-900 lh-26 font-xssss">
        <i class="feather-message-circle text-grey-900 me-1 font-xss"></i>
        <span class="d-none-xss">{{ $commentsCount }} {{ $commentsCount === 1 ? 'Comment' : 'Comments' }}</span>
    </span>
    @if($shareUrl)
        <button type="button" class="comment-action-btn ms-3 copy-post-link" data-link="{{ $shareUrl }}">
            <i class="feather-share-2"></i> Share
        </button>
    @endif
</div>
