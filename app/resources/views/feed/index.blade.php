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


        <!-- create post trigger -->
        <div class="card w-100 shadow-xss rounded-xxl border-0 ps-4 pt-3 pe-4 pb-3 mb-3">
            <div class="card-body p-0 d-flex align-items-center">
                <figure class="avatar me-3 mb-0"><img src="{{ asset('images/profile-4.png') }}" alt="image" class="shadow-sm rounded-circle w45"></figure>
                <button type="button" data-bs-toggle="modal" data-bs-target="#createPostModal"
                    class="flex-grow-1 text-start bor-0 rounded-xxl p-2 ps-4 font-xssss text-grey-500 fw-500 border-light-md theme-dark-bg bg-transparent"
                    style="cursor:pointer;">
                    What's on your mind, {{ auth()->user()->profile->display_name ?? 'there' }}?
                </button>
                <a href="#" data-bs-toggle="modal" data-bs-target="#createPostModal" class="d-flex align-items-center ms-3 text-grey-600"><i class="feather-video font-md text-danger me-1"></i></a>
                <a href="#" data-bs-toggle="modal" data-bs-target="#createPostModal" class="d-flex align-items-center ms-2 text-grey-600"><i class="feather-image font-md text-success me-1"></i></a>
                <a href="#" data-bs-toggle="modal" data-bs-target="#createPostModal" class="d-flex align-items-center ms-2 text-grey-600"><i class="feather-smile font-md text-warning"></i></a>
            </div>
        </div>

        <!-- publications -->
        @forelse($publications as $publication)
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
                <h5 class="fw-700 text-grey-900 font-xss mb-1">{{ $publication->title }}</h5>
                <p class="fw-500 text-grey-500 lh-26 font-xssss w-100">{{ $publication->text }}</p>
            </div>

            <div class="card-body d-flex p-0 mt-3">
                <form method="POST" action="{{ route('reactions.toggle', $publication->id) }}" class="d-inline me-2">
                    @csrf
                    <button type="submit" class="emoji-bttn d-flex align-items-center fw-600 text-grey-900 text-dark lh-26 font-xssss border-0 bg-transparent p-0 cursor-pointer">
                        <i class="feather-thumbs-up text-white bg-primary-gradiant me-1 btn-round-xs font-xss"></i>
                        <i class="feather-heart text-white bg-red-gradiant me-2 btn-round-xs font-xss"></i>Like
                    </button>
                </form>
                <div class="emoji-wrap">
                    <ul class="emojis list-inline mb-0">
                        <li class="emoji list-inline-item"><i class="em em---1"></i> </li>
                        <li class="emoji list-inline-item"><i class="em em-angry"></i></li>
                        <li class="emoji list-inline-item"><i class="em em-anguished"></i> </li>
                        <li class="emoji list-inline-item"><i class="em em-astonished"></i> </li>
                        <li class="emoji list-inline-item"><i class="em em-blush"></i></li>
                        <li class="emoji list-inline-item"><i class="em em-clap"></i></li>
                        <li class="emoji list-inline-item"><i class="em em-cry"></i></li>
                        <li class="emoji list-inline-item"><i class="em em-full_moon_with_face"></i></li>
                    </ul>
                </div>
                <a href="#comments-{{ $publication->id }}" class="d-flex align-items-center fw-600 text-grey-900 text-dark lh-26 font-xssss">
                    <i class="feather-message-circle text-dark text-grey-900 btn-round-sm font-lg"></i>
                    <span class="d-none-xss">{{ $publication->comments_count }} Comment</span>
                </a>
                <a href="#" id="shareMenu{{ $publication->id }}" data-bs-toggle="dropdown" aria-expanded="false" class="ms-auto d-flex align-items-center fw-600 text-grey-900 text-dark lh-26 font-xssss">
                    <i class="feather-share-2 text-grey-900 text-dark btn-round-sm font-lg"></i><span class="d-none-xs">Share</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end p-4 rounded-xxl border-0 shadow-lg" aria-labelledby="shareMenu{{ $publication->id }}">
                    <h4 class="fw-700 font-xss text-grey-900 d-flex align-items-center">Share <i class="feather-x ms-auto font-xssss btn-round-xs bg-greylight text-grey-900 me-2"></i></h4>
                    <div class="card-body p-0 d-flex">
                        <ul class="d-flex align-items-center justify-content-between mt-2">
                            <li class="me-1"><a href="#" class="btn-round-lg bg-facebook"><i class="font-xs ti-facebook text-white"></i></a></li>
                            <li class="me-1"><a href="#" class="btn-round-lg bg-twiiter"><i class="font-xs ti-twitter-alt text-white"></i></a></li>
                            <li class="me-1"><a href="#" class="btn-round-lg bg-linkedin"><i class="font-xs ti-linkedin text-white"></i></a></li>
                            <li class="me-1"><a href="#" class="btn-round-lg bg-instagram"><i class="font-xs ti-instagram text-white"></i></a></li>
                            <li><a href="#" class="btn-round-lg bg-pinterest"><i class="font-xs ti-pinterest text-white"></i></a></li>
                        </ul>
                    </div>
                    <h4 class="fw-700 font-xssss mt-4 text-grey-500 d-flex align-items-center mb-3">Copy Link</h4>
                    <i class="feather-copy position-absolute right-35 mt-3 font-xs text-grey-500"></i>
                    <input type="text" value="{{ url('/') }}" class="bg-grey text-grey-500 font-xssss border-0 lh-32 p-2 font-xssss fw-600 rounded-3 w-100 theme-dark-bg">
                </div>
            </div>

            <!-- comments -->
            <div id="comments-{{ $publication->id }}" class="card-body p-0 mt-3">
                @foreach($publication->comments as $comment)
                <div class="d-flex align-items-start mb-2">
                    <figure class="avatar me-2 mb-0"><img src="{{ asset('images/profile-4.png') }}" alt="image" class="shadow-sm rounded-circle w30"></figure>
                    <div class="bg-greylight theme-dark-bg rounded-xxl p-2 flex-fill">
                        <h5 class="fw-700 text-grey-900 font-xssss mb-1">{{ $comment->author->profile->display_name ?? 'Unknown' }}</h5>
                        <p class="fw-500 text-grey-500 font-xssss mb-0 lh-24">{{ $comment->text }}</p>
                    </div>
                    @if(auth()->id() === $comment->author_id || auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('comments.destroy', $comment->id) }}" class="ms-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-round-sm bg-greylight border-0 text-grey-500 font-xss cursor-pointer"><i class="feather-x"></i></button>
                    </form>
                    @endif
                </div>
                @endforeach

                <form method="POST" action="{{ route('comments.store', $publication->id) }}" class="d-flex mt-2">
                    @csrf
                    <figure class="avatar me-2 mb-0"><img src="{{ asset('images/profile-4.png') }}" alt="image" class="shadow-sm rounded-circle w30"></figure>
                    <div class="form-group icon-right-input style1-input mb-0 flex-fill">
                        <input type="text" name="text" placeholder="Write a comment..." class="form-control rounded-xl bg-greylight border-0 font-xssss fw-500 ps-3" required>
                        <button type="submit" class="feather-send text-grey-500 font-md border-0 bg-transparent cursor-pointer"></button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="card w-100 shadow-xss rounded-xxl border-0 p-4 mb-3 text-center">
            <p class="fw-500 text-grey-500 font-xssss mb-0">No publications yet. Be the first to post!</p>
        </div>
        @endforelse

        <!-- pagination -->
        @if($publications->hasPages())
        <div class="card w-100 shadow-xss rounded-xxl border-0 p-4 mb-3">
            {{ $publications->links() }}
        </div>
        @endif

        <div class="card w-100 text-center shadow-xss rounded-xxl border-0 p-4 mb-3 mt-3">
            <div class="snippet mt-2 ms-auto me-auto" data-title=".dot-typing">
                <div class="stage">
                    <div class="dot-typing"></div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@section('left_sidebar_extras')
<div class="nav-wrap bg-white bg-transparent-card rounded-xxl shadow-xss mb-2">
    <div class="card-body d-flex align-items-center p-4">
        <h4 class="fw-700 mb-0 font-xssss text-grey-900">Friend Request</h4>
        <a href="{{ route('members.index') }}" class="fw-600 ms-auto font-xssss text-primary">See all</a>
    </div>
    <div class="card-body d-flex pt-4 ps-4 pe-4 pb-0 border-top-xs bor-0">
        <figure class="avatar me-3"><img src="{{ asset('images/user-7.png') }}" alt="image" class="shadow-sm rounded-circle w45"></figure>
        <h4 class="fw-700 text-grey-900 font-xssss mt-1">Anthony Daugloi <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">12 mutual friends</span></h4>
    </div>
    <div class="card-body d-flex align-items-center pt-0 ps-4 pe-4 pb-4">
        <a href="#" class="p-2 lh-20 w100 bg-primary-gradiant me-2 text-white text-center font-xssss fw-600 ls-1 rounded-xl">Confirm</a>
        <a href="#" class="p-2 lh-20 w100 bg-grey text-grey-800 text-center font-xssss fw-600 ls-1 rounded-xl">Delete</a>
    </div>
    <div class="card-body d-flex pt-0 ps-4 pe-4 pb-0">
        <figure class="avatar me-3"><img src="{{ asset('images/user-8.png') }}" alt="image" class="shadow-sm rounded-circle w45"></figure>
        <h4 class="fw-700 text-grey-900 font-xssss mt-1">Mohannad Zitoun <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">12 mutual friends</span></h4>
    </div>
    <div class="card-body d-flex align-items-center pt-0 ps-4 pe-4 pb-4">
        <a href="#" class="p-2 lh-20 w100 bg-primary-gradiant me-2 text-white text-center font-xssss fw-600 ls-1 rounded-xl">Confirm</a>
        <a href="#" class="p-2 lh-20 w100 bg-grey text-grey-800 text-center font-xssss fw-600 ls-1 rounded-xl">Delete</a>
    </div>
</div>

<div class="nav-wrap bg-white bg-transparent-card rounded-xxl shadow-xss mb-2">
    <div class="card-body d-flex align-items-center p-4">
        <h4 class="fw-700 mb-0 font-xssss text-grey-900">Suggest Group</h4>
        <a href="{{ route('groups.index') }}" class="fw-600 ms-auto font-xssss text-primary">See all</a>
    </div>
    <div class="card-body d-flex pt-4 ps-4 pe-4 pb-0 overflow-hidden border-top-xs bor-0">
        <img src="{{ asset('images/e-2.jpg') }}" alt="img" class="img-fluid rounded-xxl mb-2">
    </div>
    <div class="card-body dd-block pt-0 ps-4 pe-4 pb-4">
        <ul class="memberlist mt-1 mb-2 ms-0 d-block">
            <li class="w20"><a href="#"><img src="{{ asset('images/user-6.png') }}" alt="user" class="w35 d-inline-block" style="opacity: 1;"></a></li>
            <li class="w20"><a href="#"><img src="{{ asset('images/user-7.png') }}" alt="user" class="w35 d-inline-block" style="opacity: 1;"></a></li>
            <li class="w20"><a href="#"><img src="{{ asset('images/user-8.png') }}" alt="user" class="w35 d-inline-block" style="opacity: 1;"></a></li>
            <li class="w20"><a href="#"><img src="{{ asset('images/user-3.png') }}" alt="user" class="w35 d-inline-block" style="opacity: 1;"></a></li>
            <li class="last-member"><a href="#" class="bg-greylight fw-600 text-grey-500 font-xssss w35 ls-3 text-center" style="height: 35px; line-height: 35px;">+2</a></li>
            <li class="ps-3 w-auto ms-1"><a href="#" class="fw-600 text-grey-500 font-xssss">Member apply</a></li>
        </ul>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-xxl border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-700 font-md text-grey-900" id="createPostModalLabel">Create post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="d-flex align-items-center mb-3">
                    <figure class="avatar me-3 mb-0"><img src="{{ asset('images/profile-4.png') }}" alt="image" class="shadow-sm rounded-circle w45"></figure>
                    <div>
                        <h5 class="fw-700 font-xss text-grey-900 mb-0">{{ auth()->user()->profile->display_name ?? '' }}</h5>
                    </div>
                </div>
                <form method="POST" action="{{ route('publications.store') }}" id="createPostForm">
                    @csrf
                    <input type="text" name="title" class="bor-0 w-100 rounded-xxl p-2 ps-3 font-xssss text-grey-500 fw-500 border-light-md theme-dark-bg mb-2" placeholder="Post title" required>
                    <textarea name="text" class="bor-0 w-100 rounded-xxl p-2 ps-3 font-xssss text-grey-500 fw-500 border-light-md theme-dark-bg" rows="5" placeholder="What's on your mind, {{ auth()->user()->profile->display_name ?? 'there' }}?" required></textarea>
                    <div class="d-flex align-items-center border-light-md rounded-xxl p-2 mt-3">
                        <span class="font-xssss fw-600 text-grey-500 me-auto">Add to your post</span>
                        <a href="#" class="ms-3 text-grey-600"><i class="feather-image font-md text-success"></i></a>
                    </div>
                    <button type="submit" class="w-100 mt-3 p-2 lh-20 bg-primary-gradiant text-white text-center font-xssss fw-600 ls-1 rounded-xl border-0 cursor-pointer">Post</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush
