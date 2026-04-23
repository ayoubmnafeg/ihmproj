@extends('layouts.app')

@section('title', 'Newsfeed')

@section('content')
<div class="row feed-body">
    <div class="col-xl-8 col-lg-9 mx-auto">

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


        <livewire:create-post context="feed" modal-id="createPostModalFeed" />

        <livewire:post-feed scope="all" />

    </div>

</div>
@endsection

@section('left_sidebar_extras')
<div class="nav-wrap bg-white bg-transparent-card rounded-xxl shadow-xss mb-2">
    <div class="card-body d-flex align-items-center p-4">
        <h4 class="fw-700 mb-0 font-xssss text-grey-900">Friend Requests</h4>
        <a href="{{ route('members.index') }}" class="fw-600 ms-auto font-xssss text-primary">See all</a>
    </div>
    @forelse($incomingFriendRequests as $friendRequest)
        <div class="card-body d-flex pt-4 ps-4 pe-4 pb-0 border-top-xs bor-0">
            <figure class="avatar me-3"><img src="{{ $friendRequest->sender->profile->avatar_url ?: asset('images/user-12.png') }}" alt="image" class="shadow-sm rounded-circle w45"></figure>
            <h4 class="fw-700 text-grey-900 font-xssss mt-1">
                {{ $friendRequest->sender->profile->display_name ?: ('anon_' . $friendRequest->sender->id) }}
                <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">Sent you a friend request</span>
            </h4>
        </div>
        <div class="card-body d-flex align-items-center pt-0 ps-4 pe-4 pb-4">
            <form method="POST" action="{{ route('friend-requests.update', $friendRequest->id) }}" class="w-100 me-2">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="accepted">
                <button type="submit" class="p-2 lh-20 w-100 bg-primary-gradiant border-0 text-white text-center font-xssss fw-600 ls-1 rounded-xl">Confirm</button>
            </form>
            <form method="POST" action="{{ route('friend-requests.update', $friendRequest->id) }}" class="w-100">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="rejected">
                <button type="submit" class="p-2 lh-20 w-100 bg-grey border-0 text-grey-800 text-center font-xssss fw-600 ls-1 rounded-xl">Delete</button>
            </form>
        </div>
    @empty
        <div class="card-body pt-0 ps-4 pe-4 pb-4 border-top-xs">
            <p class="fw-500 text-grey-500 font-xssss mb-0">No pending friend requests.</p>
        </div>
    @endforelse
</div>

<div class="nav-wrap bg-white bg-transparent-card rounded-xxl shadow-xss mb-2">
    <div class="card-body d-flex align-items-center p-4">
        <h4 class="fw-700 mb-0 font-xssss text-grey-900">Suggest Group</h4>
        <a href="{{ route('groups.index') }}" class="fw-600 ms-auto font-xssss text-primary">See all</a>
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
                    <button type="submit" class="p-2 lh-20 bg-primary-gradiant border-0 text-white text-center font-xssss fw-600 ls-1 rounded-xl">Follow</button>
                </form>
            </div>
        @empty
            <p class="fw-500 text-grey-500 font-xssss mb-0 pt-2">No new groups to suggest right now.</p>
        @endforelse
    </div>
</div>
@endsection

