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

@section('left_sidebar_extras')
@auth
<div class="nav-wrap bg-white bg-transparent-card rounded-xxl shadow-xss mb-2">
    <div class="card-body d-flex align-items-center p-4">
        <h4 class="fw-600 mb-0 font-xssss text-grey-700">{{ __('Connection requests') }}</h4>
        <a href="{{ route('members.index') }}" class="fw-600 ms-auto font-xssss text-primary">{{ __('View all') }}</a>
    </div>
    @forelse($incomingFriendRequests as $friendRequest)
        <div class="card-body d-flex pt-4 ps-4 pe-4 pb-0 border-top-xs bor-0">
            <figure class="avatar me-3"><img src="{{ $friendRequest->sender->profile->avatar_url ?: asset('images/user-12.png') }}" alt="image" class="shadow-sm rounded-circle w45"></figure>
            <h4 class="fw-700 text-grey-900 font-xssss mt-1">
                {{ $friendRequest->sender->profile->display_name ?: ('anon_' . $friendRequest->sender->id) }}
                <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">{{ __('Sent you a friend request') }}</span>
            </h4>
        </div>
        <div class="card-body d-flex align-items-center pt-0 ps-4 pe-4 pb-4">
            <form method="POST" action="{{ route('friend-requests.update', $friendRequest->id) }}" class="w-100 me-2">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="accepted">
                <button type="submit" class="p-2 lh-20 w-100 bg-primary-gradiant border-0 text-white text-center font-xssss fw-600 ls-1 rounded-xl">{{ __('Confirm') }}</button>
            </form>
            <form method="POST" action="{{ route('friend-requests.update', $friendRequest->id) }}" class="w-100">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="rejected">
                <button type="submit" class="p-2 lh-20 w-100 bg-grey border-0 text-grey-800 text-center font-xssss fw-600 ls-1 rounded-xl">{{ __('Delete') }}</button>
            </form>
        </div>
    @empty
        <div class="card-body pt-0 ps-4 pe-4 pb-4 border-top-xs">
            <p class="fw-500 text-grey-500 font-xssss mb-0">{{ __('No pending friend requests.') }}</p>
        </div>
    @endforelse
</div>

<div class="nav-wrap bg-white bg-transparent-card rounded-xxl shadow-xss mb-2">
    <div class="card-body d-flex align-items-center p-4">
        <h4 class="fw-600 mb-0 font-xssss text-grey-700">{{ __('Suggested spaces') }}</h4>
        <a href="{{ route('groups.index') }}" class="fw-600 ms-auto font-xssss text-primary">{{ __('Browse') }}</a>
    </div>
    <div class="card-body pt-2 ps-4 pe-4 pb-4 border-top-xs">
        @forelse($suggestedGroups as $group)
            <div class="d-flex align-items-center {{ $loop->first ? 'pt-2' : 'pt-3' }} {{ $loop->last ? 'pb-0' : 'pb-3' }} {{ !$loop->last ? 'border-bottom' : '' }}">
                <figure class="avatar me-3 mb-0">
                    <img src="{{ $group->profile_image_path ? asset('storage/' . $group->profile_image_path) : asset('images/user-12.png') }}" alt="{{ $group->name }}" class="shadow-sm rounded-circle w45">
                </figure>
                <div class="flex-grow-1">
                    <h4 class="fw-700 text-grey-900 font-xssss mb-0">
                        <a href="{{ route('groups.show', $group->id) }}" class="text-dark">{{ $group->name }}</a>
                    </h4>
                    <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">{{ $group->followers_count }} follower{{ $group->followers_count === 1 ? '' : 's' }}</span>
                </div>
                <form method="POST" action="{{ route('groups.follow', $group->id) }}">
                    @csrf
                    <button type="submit" class="p-2 lh-20 bg-primary-gradiant border-0 text-white text-center font-xssss fw-600 ls-1 rounded-xl">{{ __('Follow') }}</button>
                </form>
            </div>
        @empty
            <p class="fw-500 text-grey-500 font-xssss mb-0 pt-2">{{ __('No new groups to suggest right now.') }}</p>
        @endforelse
    </div>
</div>
@endauth
@endsection

