@extends('layouts.app')

@section('title', 'Group Page')

@section('content')
<div class="row">
    <div class="col-xl-4 col-xxl-3 col-lg-4 pe-0">
        <div class="card w-100 shadow-xss rounded-xxl overflow-hidden border-0 mb-3 mt-3 pb-3">
            <div class="card-body position-relative h150 bg-image-cover bg-image-center" style="background-image: url('{{ asset('images/bb-9.jpg') }}');"></div>
            <div class="card-body d-block pt-4 text-center">
                <figure class="avatar mt--6 position-relative w75 z-index-1 w100 ms-auto me-auto">
                    <img src="{{ asset('images/pt-1.jpg') }}" alt="group avatar" class="p-1 bg-white rounded-xl w-100">
                </figure>
                <h4 class="font-xs ls-1 fw-700 text-grey-900">Surfiya Zakir
                    <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">@surfiyazakir22</span>
                </h4>
            </div>
            <div class="card-body d-flex align-items-center ps-4 pe-4 pt-0">
                <h4 class="font-xsssss text-center text-grey-500 fw-600 ms-2 me-2"><b class="text-grey-900 mb-1 font-xss fw-700 d-inline-block ls-3 text-dark">456</b> Posts</h4>
                <h4 class="font-xsssss text-center text-grey-500 fw-600 ms-2 me-2"><b class="text-grey-900 mb-1 font-xss fw-700 d-inline-block ls-3 text-dark">2.1k</b> Followers</h4>
                <h4 class="font-xsssss text-center text-grey-500 fw-600 ms-2 me-2"><b class="text-grey-900 mb-1 font-xss fw-700 d-inline-block ls-3 text-dark">32k</b> Follow</h4>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center ps-4 pe-4 pt-0">
                <a href="#" class="bg-success p-3 z-index-1 rounded-3 text-white font-xsssss text-uppercase fw-700 ls-3">Add Friend</a>
                <a href="#" class="bg-greylight btn-round-lg ms-2 rounded-3 text-grey-700"><i class="feather-mail font-md"></i></a>
                <a href="#" class="bg-greylight btn-round-lg ms-2 rounded-3 text-grey-700"><i class="ti-more font-md"></i></a>
            </div>
        </div>

        <div class="card w-100 shadow-xss rounded-xxl border-0 mb-3">
            <div class="card-body d-block p-4">
                <h4 class="fw-700 mb-3 font-xsss text-grey-900">About</h4>
                <p class="fw-500 text-grey-500 lh-24 font-xssss mb-0">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nulla dolor, ornare at commodo non, feugiat non nisi.
                    Phasellus faucibus mollis pharetra. Proin blandit ac massa sed rhoncus.
                </p>
            </div>
            <div class="card-body border-top-xs d-flex">
                <i class="feather-lock text-grey-500 me-3 font-lg"></i>
                <h4 class="fw-700 text-grey-900 font-xssss mt-0">Private <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">What's up, how are you?</span></h4>
            </div>
            <div class="card-body d-flex pt-0">
                <i class="feather-eye text-grey-500 me-3 font-lg"></i>
                <h4 class="fw-700 text-grey-900 font-xssss mt-0">Visible <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">Anyone can find you</span></h4>
            </div>
            <div class="card-body d-flex pt-0">
                <i class="feather-map-pin text-grey-500 me-3 font-lg"></i>
                <h4 class="fw-700 text-grey-900 font-xssss mt-1">Flodia, Austia</h4>
            </div>
            <div class="card-body d-flex pt-0">
                <i class="feather-users text-grey-500 me-3 font-lg"></i>
                <h4 class="fw-700 text-grey-900 font-xssss mt-1">General Group</h4>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-xxl-9 col-lg-8">
        <div class="card w-100 shadow-xss rounded-xxl border-0 ps-4 pt-4 pe-4 pb-3 mb-3 mt-3">
            <div class="card-body p-0">
                <a href="#" class="font-xssss fw-600 text-grey-500 card-body p-0 d-flex align-items-center">
                    <i class="btn-round-sm font-xs text-primary feather-edit-3 me-2 bg-greylight"></i>Create Post
                </a>
            </div>
            <div class="card-body p-0 mt-3 position-relative">
                <figure class="avatar position-absolute ms-2 mt-1 top-5"><img src="{{ asset('images/user-8.png') }}" alt="image" class="shadow-sm rounded-circle w30"></figure>
                <textarea name="message" class="h100 bor-0 w-100 rounded-xxl p-2 ps-5 font-xssss text-grey-500 fw-500 border-light-md theme-dark-bg" cols="30" rows="10" placeholder="What's on your mind?"></textarea>
            </div>
            <div class="card-body d-flex p-0 mt-0">
                <a href="#" class="d-flex align-items-center font-xssss fw-600 ls-1 text-grey-700 text-dark pe-4"><i class="font-md text-danger feather-video me-2"></i><span class="d-none-xs">Live Video</span></a>
                <a href="#" class="d-flex align-items-center font-xssss fw-600 ls-1 text-grey-700 text-dark pe-4"><i class="font-md text-success feather-image me-2"></i><span class="d-none-xs">Photo/Video</span></a>
                <a href="#" class="d-flex align-items-center font-xssss fw-600 ls-1 text-grey-700 text-dark pe-4"><i class="font-md text-warning feather-camera me-2"></i><span class="d-none-xs">Feeling/Activity</span></a>
                <a href="#" class="ms-auto"><i class="ti-more-alt text-grey-900 btn-round-md bg-greylight font-xss"></i></a>
            </div>
        </div>

        <div class="card w-100 shadow-xss rounded-xxl border-0 p-4 mb-3">
            <div class="card-body p-0 d-flex">
                <figure class="avatar me-3"><img src="{{ asset('images/user-7.png') }}" alt="image" class="shadow-sm rounded-circle w45"></figure>
                <h4 class="fw-700 text-grey-900 font-xssss mt-1">Anthony Daugloi <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">3 hour ago</span></h4>
                <a href="#" class="ms-auto"><i class="ti-more-alt text-grey-900 btn-round-md bg-greylight font-xss"></i></a>
            </div>
            <div class="card-body p-0 me-lg-5">
                <p class="fw-500 text-grey-500 lh-26 font-xssss w-100">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nulla dolor, ornare at commodo non, feugiat non nisi.
                    <a href="#" class="fw-600 text-primary ms-2">See more</a>
                </p>
            </div>
            <div class="card-body d-block p-0">
                <div class="row ps-2 pe-2">
                    <div class="col-xs-4 col-sm-4 p-1"><a href="{{ asset('images/t-10.jpg') }}" data-lightbox="roadtrip"><img src="{{ asset('images/t-10.jpg') }}" class="rounded-3 w-100" alt="image"></a></div>
                    <div class="col-xs-4 col-sm-4 p-1"><a href="{{ asset('images/t-11.jpg') }}" data-lightbox="roadtrip"><img src="{{ asset('images/t-11.jpg') }}" class="rounded-3 w-100" alt="image"></a></div>
                    <div class="col-xs-4 col-sm-4 p-1"><a href="{{ asset('images/t-12.jpg') }}" data-lightbox="roadtrip" class="position-relative d-block"><img src="{{ asset('images/t-12.jpg') }}" class="rounded-3 w-100" alt="image"><span class="img-count font-sm text-white ls-3 fw-600"><b>+2</b></span></a></div>
                </div>
            </div>
            <div class="card-body d-flex p-0 mt-3">
                <a href="#" class="d-flex align-items-center fw-600 text-grey-900 text-dark lh-26 font-xssss me-3"><i class="feather-thumbs-up text-white bg-primary-gradiant me-1 btn-round-xs font-xss"></i><i class="feather-heart text-white bg-red-gradiant me-2 btn-round-xs font-xss"></i>2.8K Like</a>
                <a href="#" class="d-flex align-items-center fw-600 text-grey-900 text-dark lh-26 font-xssss"><i class="feather-message-circle text-dark text-grey-900 btn-round-sm font-lg"></i>22 Comment</a>
                <a href="#" class="ms-auto d-flex align-items-center fw-600 text-grey-900 text-dark lh-26 font-xssss"><i class="feather-share-2 text-grey-900 text-dark btn-round-sm font-lg"></i><span class="d-none-xs">Share</span></a>
            </div>
        </div>
    </div>
</div>
@endsection
