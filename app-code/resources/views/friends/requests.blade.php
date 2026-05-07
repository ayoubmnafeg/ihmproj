@extends('layouts.app')

@section('title', 'Friend Requests')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card shadow-xss w-100 d-block d-flex border-0 p-4 mb-3">
            <div class="card-body d-flex align-items-center p-0">
                <h2 class="fw-700 mb-0 mt-0 font-md text-grey-900">Friend Requests</h2>
                <div class="search-form-2 ms-auto">
                    <i class="ti-search font-xss"></i>
                    <input type="text" class="form-control text-grey-500 mb-0 bg-greylight theme-dark-bg border-0" placeholder="Search here.">
                </div>
                <a href="#" class="btn-round-md ms-2 bg-greylight theme-dark-bg rounded-3"><i class="feather-filter font-xss text-grey-500"></i></a>
            </div>
        </div>

        <div class="row ps-2 pe-2">
            @forelse ($incomingRequests as $friendRequest)
                <div class="col-md-3 col-sm-4 pe-2 ps-2">
                    <div class="card d-block border-0 shadow-xss rounded-3 overflow-hidden mb-3">
                        <div class="card-body d-block w-100 ps-3 pe-3 pb-4 text-center">
                            <figure class="avatar ms-auto me-auto mb-0 position-relative w65 z-index-1">
                                <img src="{{ $friendRequest->sender->profile->avatar_url ?: asset('images/user-12.png') }}" alt="image" class="float-right p-0 bg-white rounded-circle w-100 shadow-xss">
                            </figure>
                            <div class="clearfix"></div>
                            <h4 class="fw-700 font-xsss mt-3 mb-1">
                                <a href="{{ route('profile.show', $friendRequest->sender_id) }}" class="text-grey-900">
                                    {{ $friendRequest->sender->profile->display_name ?: ('anon_' . $friendRequest->sender->id) }}
                                </a>
                            </h4>
                            <p class="fw-500 font-xsssss text-grey-500 mt-0 mb-3">
                                <a href="{{ route('profile.show', $friendRequest->sender_id) }}" class="text-grey-500">
                                    View profile
                                </a>
                            </p>
                            <form method="POST" action="{{ route('friend-requests.update', $friendRequest->id) }}" class="d-inline-block">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="accepted">
                                <button type="submit" class="mt-0 btn pt-2 pb-2 ps-3 pe-3 lh-24 ms-1 ls-3 d-inline-block rounded-xl bg-success border-0 font-xsssss fw-700 ls-lg text-white">
                                    CONFIRM
                                </button>
                            </form>
                            <form method="POST" action="{{ route('friend-requests.update', $friendRequest->id) }}" class="d-inline-block">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="rejected">
                                <button type="submit" class="mt-0 btn pt-2 pb-2 ps-3 pe-3 lh-24 ms-1 ls-3 d-inline-block rounded-xl bg-grey border-0 font-xsssss fw-700 ls-lg text-grey-800">
                                    DELETE
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card d-block border-0 shadow-xss rounded-3 overflow-hidden mb-3">
                        <div class="card-body d-block w-100 ps-3 pe-3 pb-4 pt-4 text-center">
                            <h4 class="fw-700 font-xsss mt-0 mb-1">No pending requests</h4>
                            <p class="fw-500 font-xsssss text-grey-500 mt-0 mb-0">You are all caught up.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="card shadow-xss w-100 d-block d-flex border-0 p-4 mb-3 mt-4">
            <div class="card-body d-flex align-items-center p-0">
                <h2 class="fw-700 mb-0 mt-0 font-md text-grey-900">Sent Requests</h2>
            </div>
        </div>

        <div class="row ps-2 pe-2">
            @forelse ($sentRequests as $friendRequest)
                <div class="col-md-3 col-sm-4 pe-2 ps-2">
                    <div class="card d-block border-0 shadow-xss rounded-3 overflow-hidden mb-3">
                        <div class="card-body d-block w-100 ps-3 pe-3 pb-4 text-center">
                            <figure class="avatar ms-auto me-auto mb-0 position-relative w65 z-index-1">
                                <img src="{{ $friendRequest->receiver->profile->avatar_url ?: asset('images/user-12.png') }}" alt="image" class="float-right p-0 bg-white rounded-circle w-100 shadow-xss">
                            </figure>
                            <div class="clearfix"></div>
                            <h4 class="fw-700 font-xsss mt-3 mb-1">
                                <a href="{{ route('profile.show', $friendRequest->receiver_id) }}" class="text-grey-900">
                                    {{ $friendRequest->receiver->profile->display_name ?: ('anon_' . $friendRequest->receiver->id) }}
                                </a>
                            </h4>
                            <p class="fw-500 font-xsssss text-grey-500 mt-0 mb-3">
                                <a href="{{ route('profile.show', $friendRequest->receiver_id) }}" class="text-grey-500">
                                    View profile
                                </a>
                            </p>
                            <form method="POST" action="{{ route('friend-requests.update', $friendRequest->id) }}" class="d-inline-block">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="canceled">
                                <button type="submit" class="mt-0 btn pt-2 pb-2 ps-3 pe-3 lh-24 ms-1 ls-3 d-inline-block rounded-xl bg-grey border-0 font-xsssss fw-700 ls-lg text-grey-800">
                                    CANCEL
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card d-block border-0 shadow-xss rounded-3 overflow-hidden mb-3">
                        <div class="card-body d-block w-100 ps-3 pe-3 pb-4 pt-4 text-center">
                            <h4 class="fw-700 font-xsss mt-0 mb-1">No sent requests</h4>
                            <p class="fw-500 font-xsssss text-grey-500 mt-0 mb-0">Send requests from user profiles.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="card shadow-xss w-100 d-block d-flex border-0 p-4 mb-3 mt-4">
            <div class="card-body d-flex align-items-center p-0">
                <h2 class="fw-700 mb-0 mt-0 font-md text-grey-900">My Friends</h2>
            </div>
        </div>

        <div class="row ps-2 pe-2">
            @forelse ($friends as $friendLink)
                @php
                    $friend = $friendLink->sender_id === auth()->id() ? $friendLink->receiver : $friendLink->sender;
                @endphp
                <div class="col-md-3 col-sm-4 pe-2 ps-2">
                    <div class="card d-block border-0 shadow-xss rounded-3 overflow-hidden mb-3">
                        <div class="card-body d-block w-100 ps-3 pe-3 pb-4 text-center">
                            <figure class="avatar ms-auto me-auto mb-0 position-relative w65 z-index-1">
                                <img src="{{ $friend->profile->avatar_url ?: asset('images/user-12.png') }}" alt="image" class="float-right p-0 bg-white rounded-circle w-100 shadow-xss">
                            </figure>
                            <div class="clearfix"></div>
                            <h4 class="fw-700 font-xsss mt-3 mb-1">
                                <a href="{{ route('profile.show', $friend->id) }}" class="text-grey-900">
                                    {{ $friend->profile->display_name ?: ('anon_' . $friend->id) }}
                                </a>
                            </h4>
                            <p class="fw-500 font-xsssss text-grey-500 mt-0 mb-3">Connected friend</p>
                            <button type="button" class="mt-0 btn pt-2 pb-2 ps-3 pe-3 lh-24 ms-1 ls-3 d-inline-block rounded-xl bg-success border-0 font-xsssss fw-700 ls-lg text-white" disabled>
                                FRIEND
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card d-block border-0 shadow-xss rounded-3 overflow-hidden mb-3">
                        <div class="card-body d-block w-100 ps-3 pe-3 pb-4 pt-4 text-center">
                            <h4 class="fw-700 font-xsss mt-0 mb-1">No friends yet</h4>
                            <p class="fw-500 font-xsssss text-grey-500 mt-0 mb-0">Accept requests to build your friends list.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
