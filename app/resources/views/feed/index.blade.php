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

