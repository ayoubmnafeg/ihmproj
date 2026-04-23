@extends('layouts.app')

@section('title', 'Profile')

@section('content')
@php
    $isOwnProfile = auth()->id() === $user->id;
@endphp

<div class="row">
    <div class="col-lg-12">
        <div class="card w-100 border-0 p-0 bg-white shadow-xss rounded-xxl">
            <div class="card-body h250 p-0 rounded-xxl overflow-hidden m-3">
                <img src="{{ asset('images/u-bg.jpg') }}" alt="image">
            </div>
            <div class="card-body p-0 position-relative">
                <figure class="avatar position-absolute w100 z-index-1" style="top:-40px; left: 30px;">
                    <img src="{{ $user->profile->avatar_url ?: asset('images/user-12.png') }}" alt="avatar" class="float-right p-1 bg-white rounded-circle w-100">
                </figure>
                <h4 class="fw-700 font-sm mt-2 mb-lg-5 mb-4 pl-15">
                    {{ $user->profile->display_name ?: ('anon_' . $user->id) }}
                </h4>
                <div class="d-flex align-items-center justify-content-center position-absolute-md right-15 top-0 me-2">
                    @if ($isOwnProfile)
                        <a href="{{ route('profile.edit') }}" class="d-none d-lg-block bg-primary-gradiant p-3 z-index-1 rounded-3 text-white font-xsssss text-uppercase fw-700 ls-3 border-0">
                            Edit Profile
                        </a>
                    @else
                        @if ($friendRequestStatus === 'friends')
                            <button type="button" class="d-none d-lg-block bg-success p-3 z-index-1 rounded-3 text-white font-xsssss text-uppercase fw-700 ls-3 border-0" disabled>
                                Friends
                            </button>
                        @elseif ($friendRequestStatus === 'outgoing_pending')
                            <form method="POST" action="{{ route('friend-requests.update', $outgoingPendingRequestId) }}" class="d-none d-lg-block">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="canceled">
                                <button type="submit" class="bg-grey p-3 z-index-1 rounded-3 text-grey-800 font-xsssss text-uppercase fw-700 ls-3 border-0">
                                    Cancel Request
                                </button>
                            </form>
                        @elseif ($friendRequestStatus === 'incoming_pending')
                            <a href="{{ route('members.index') }}" class="d-none d-lg-block bg-primary-gradiant p-3 z-index-1 rounded-3 text-white font-xsssss text-uppercase fw-700 ls-3 border-0">
                                Respond Request
                            </a>
                        @else
                            <form method="POST" action="{{ route('friend-requests.store', $user->id) }}" class="d-none d-lg-block">
                                @csrf
                                <button type="submit" class="bg-success p-3 z-index-1 rounded-3 text-white font-xsssss text-uppercase fw-700 ls-3 border-0">
                                    Add Friend
                                </button>
                            </form>
                        @endif
                    @endif
                    <a href="#" class="d-none d-lg-block bg-greylight btn-round-lg ms-2 rounded-3 text-grey-700"><i class="feather-mail font-md"></i></a>
                    <a href="#" class="d-none d-lg-block bg-greylight btn-round-lg ms-2 rounded-3 text-grey-700"><i class="ti-more font-md text-dark"></i></a>
                </div>
            </div>

            <div class="card-body d-block w-100 shadow-none mb-0 p-0 border-top-xs"></div>
        </div>
    </div>
    <div class="col-xl-12 col-xxl-12 col-lg-12">
        <div class="mt-3">
        <livewire:post-feed scope="user" :user-id="$user->id" />
        </div>
    </div>
</div>
@endsection
