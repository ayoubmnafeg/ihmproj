@extends('layouts.app')

@section('title', 'Feed')

@section('content')
<div class="row feed-body">
    <div class="col-xl-10 col-lg-10 mx-auto">

        <!-- loader wrapper -->
        <div class="preloader-wrap p-3">
            <div class="box shimmer">
                <div class="lines">
                    <div class="line s_shimmer"></div>
                    <div class="line s_shimmer"></div>
                    <div class="line s_shimmer"></div>
                    <div class="line s_shimmer"></div>
                </div>
            </div>
            <div class="box shimmer mb-3">
                <div class="lines">
                    <div class="line s_shimmer"></div>
                    <div class="line s_shimmer"></div>
                    <div class="line s_shimmer"></div>
                    <div class="line s_shimmer"></div>
                </div>
            </div>
            <div class="box shimmer">
                <div class="lines">
                    <div class="line s_shimmer"></div>
                    <div class="line s_shimmer"></div>
                    <div class="line s_shimmer"></div>
                    <div class="line s_shimmer"></div>
                </div>
            </div>
        </div>
        <!-- loader wrapper -->


        @auth
        <livewire:create-post context="feed" modal-id="createPostModalFeed" />
        @endauth

        <livewire:post-feed scope="all" />

    </div>

</div>

{{-- Scroll-to-top with circular progress ring --}}
<div id="scroll-to-top-wrap" onclick="scrollToTopFlash(this)" title="{{ __('Back to top') }}" aria-label="{{ __('Back to top') }}" role="button" tabindex="0">
    <svg id="scroll-progress-ring" viewBox="0 0 50 50">
        <circle class="ring-track" cx="25" cy="25" r="21"/>
        <circle id="ring-fill" class="ring-fill" cx="25" cy="25" r="21"/>
    </svg>
    <i class="feather-arrow-up scroll-arrow-icon"></i>
</div>

@push('modals')
<style>
    #scroll-to-top-wrap {
        position: fixed;
        bottom: 70px;
        right: 18px;
        z-index: 9999;
        width: 54px;
        height: 54px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: translateY(20px) scale(0.85);
        transition: opacity 0.3s ease, transform 0.3s ease;
        pointer-events: none;
        user-select: none;
    }
    #scroll-to-top-wrap.visible {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }
    #scroll-to-top-wrap:hover {
        transform: translateY(-2px) scale(1.06);
    }
    #scroll-to-top-wrap.flash-pulse {
        animation: flashPulse 0.55s ease forwards;
    }
    @keyframes flashPulse {
        0%   { box-shadow: 0 0 0 0 rgba(59,130,246,0.75); transform: scale(1.05); }
        50%  { box-shadow: 0 0 0 16px rgba(59,130,246,0);  transform: scale(1.16); }
        100% { box-shadow: none; transform: scale(1); }
    }
    /* Dark inner disc */
    #scroll-to-top-wrap::before {
        content: '';
        position: absolute;
        inset: 5px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        box-shadow: 0 4px 14px rgba(59,130,246,0.3);
        z-index: 0;
    }
    /* SVG Ring */
    #scroll-progress-ring {
        position: absolute;
        inset: 0;
        width: 54px;
        height: 54px;
        transform: rotate(-90deg);
        z-index: 1;
    }
    .ring-track {
        fill: rgba(30,41,59,0.12);
        stroke: rgba(59,130,246,0.18);
        stroke-width: 4;
    }
    .ring-fill {
        fill: none;
        stroke: #3b82f6;
        stroke-width: 4;
        stroke-linecap: round;
        stroke-dasharray: 132;
        stroke-dashoffset: 132;
        transition: stroke-dashoffset 0.15s ease;
    }
    .scroll-arrow-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 16px;
        color: #fff;
        z-index: 2;
    }
</style>
<script>
    (function () {
        var wrap = document.getElementById('scroll-to-top-wrap');
        var ring = document.getElementById('ring-fill');
        var CIRC = 132;
        if (!wrap || !ring) return;

        function update() {
            var scrollTop = window.scrollY || document.documentElement.scrollTop;
            var docHeight = document.documentElement.scrollHeight - window.innerHeight;
            var progress  = docHeight > 0 ? Math.min(scrollTop / docHeight, 1) : 0;
            var percent   = Math.round(progress * 100);
            ring.style.strokeDashoffset = CIRC - progress * CIRC;
            if (scrollTop > 300) { wrap.classList.add('visible'); }
            else { wrap.classList.remove('visible'); }
        }
        window.addEventListener('scroll', update, { passive: true });
        update();
    })();

    function scrollToTopFlash(el) {
        el.classList.remove('flash-pulse');
        void el.offsetWidth;
        el.classList.add('flash-pulse');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
@endpush
@endsection

@section('right_sidebar_extra')
@auth
<div class="section full pe-3 ps-4 pt-4 pb-4 position-relative feed-body connection-requests-panel">
    <div class="connection-requests-panel__header">
        <h2 class="connection-requests-panel__title">{{ __('Connection requests') }}</h2>
        <a href="{{ route('members.index') }}" class="connection-requests-panel__view-all">{{ __('View all') }}</a>
    </div>
    @forelse($incomingFriendRequests as $friendRequest)
        <div class="connection-requests-panel__item {{ $loop->first ? 'connection-requests-panel__item--first' : '' }}">
            <figure class="connection-requests-panel__avatar avatar mb-0 flex-shrink-0">
                <img src="{{ $friendRequest->sender->profile->avatar_url ?: asset('images/user-12.png') }}" alt="" width="44" height="44" class="rounded-circle shadow-sm">
            </figure>
            <div class="connection-requests-panel__body">
                <p class="connection-requests-panel__name">{{ $friendRequest->sender->profile->display_name ?: ('anon_' . $friendRequest->sender->id) }}</p>
                <p class="connection-requests-panel__hint">{{ __('Sent you a friend request') }}</p>
                <div class="connection-requests-panel__actions">
                    <form method="POST" action="{{ route('friend-requests.update', $friendRequest->id) }}" class="connection-requests-panel__form">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="accepted">
                        <button type="submit" class="connection-requests-btn connection-requests-btn--confirm">{{ __('Confirm') }}</button>
                    </form>
                    <form method="POST" action="{{ route('friend-requests.update', $friendRequest->id) }}" class="connection-requests-panel__form">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="rejected">
                        <button type="submit" class="connection-requests-btn connection-requests-btn--delete">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="connection-requests-panel__empty">{{ __('No pending friend requests.') }}</p>
    @endforelse
</div>
@endauth
@endsection

@section('left_sidebar_extras')
@auth
<div class="sidebar-spaces-panel sidebar-spaces-panel--suggested px-3 pb-2 mb-2">
    <button
        type="button"
        class="sidebar-spaces-heading"
        aria-expanded="true"
        aria-controls="sidebar-suggested-spaces-body"
        id="sidebar-suggested-spaces-heading"
    >
        <span class="sidebar-spaces-heading-label">{{ __('Suggested spaces') }}</span>
        <i class="feather-chevron-down sidebar-spaces-chevron" aria-hidden="true"></i>
    </button>
    <div class="sidebar-spaces-body pt-2" id="sidebar-suggested-spaces-body" role="region" aria-labelledby="sidebar-suggested-spaces-heading">
        <a href="{{ route('groups.index') }}" class="sidebar-spaces-manage font-xsss fw-600">
            <i class="feather-compass" aria-hidden="true"></i><span>{{ __('Browse') }}</span>
        </a>
        <div class="sidebar-spaces-rows">
            @forelse($suggestedSpaces as $space)
                @php
                    $__sCompact = preg_replace('/\s+/u', '', $space->name ?? '') ?: '';
                    $__sInitials = strtoupper(\Illuminate\Support\Str::substr($__sCompact, 0, 2) ?: '__');
                @endphp
                <div class="sidebar-space-row">
                    <a href="{{ route('groups.show', $space->id) }}" class="sidebar-space-link sidebar-space-link--stacked">
                        @if($space->profile_image_path)
                            <img src="{{ asset('storage/' . $space->profile_image_path) }}" alt="" width="28" height="28" class="sidebar-space-avatar sidebar-space-avatar-img rounded-circle shadow-sm">
                        @else
                            <span class="sidebar-space-avatar sidebar-space-avatar-label rounded-circle">{{ $__sInitials }}</span>
                        @endif
                        <span class="sidebar-space-stack">
                            <span class="sidebar-space-name text-truncate font-xsss fw-600">{{ $space->name }}</span>
                            <span class="sidebar-space-meta-sidebar sidebar-spaces-muted font-xsss fw-600">{{ $space->followers_count }} follower{{ $space->followers_count === 1 ? '' : 's' }}</span>
                        </span>
                    </a>
                    <form method="POST" action="{{ route('groups.follow', $space->id) }}" class="sidebar-suggested-follow-form">
                        @csrf
                        <button type="submit" class="sidebar-suggested-follow-btn">{{ __('Follow') }}</button>
                    </form>
                </div>
            @empty
                <p class="sidebar-spaces-muted font-xsss fw-600 mb-0 pt-2 pb-2">{{ __('No new spaces to suggest right now.') }}</p>
            @endforelse
        </div>
    </div>
</div>
@endauth
@endsection

