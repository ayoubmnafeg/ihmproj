@extends('layouts.app')

@section('title', $group->name)

@section('content')
<div class="row">
    <div class="col-xl-4 col-xxl-3 col-lg-4 pe-0">
        <div class="card w-100 shadow-xss rounded-xxl overflow-hidden border-0 mb-3 mt-3 pb-3">
            <div class="card-body position-relative h150 bg-image-cover bg-image-center" style="background-image: url('{{ asset('images/bb-9.jpg') }}');"></div>
            <div class="card-body d-block pt-4 text-center">
                <figure class="avatar mt--6 position-relative w75 z-index-1 w100 ms-auto me-auto">
                    <img src="{{ $group->profile_image_path ? asset('storage/' . $group->profile_image_path) : asset('images/user-12.png') }}" alt="group avatar" class="p-1 bg-white rounded-xl w-100">
                </figure>
                <h4 class="font-xs ls-1 fw-700 text-grey-900">{{ $group->name }}
                    <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">Category</span>
                </h4>
            </div>
            <div class="card-body d-flex align-items-center ps-4 pe-4 pt-0">
                <h4 class="font-xsssss text-center text-grey-500 fw-600 ms-2 me-2"><b class="text-grey-900 mb-1 font-xss fw-700 d-inline-block ls-3 text-dark">{{ $group->publications_count }}</b> Posts</h4>
                <h4 class="font-xsssss text-center text-grey-500 fw-600 ms-2 me-2"><b class="text-grey-900 mb-1 font-xss fw-700 d-inline-block ls-3 text-dark">{{ $group->followers_count }}</b> Followers</h4>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center ps-4 pe-4 pt-0">
                @if($isFollowing)
                    <form method="POST" action="{{ route('groups.follow', $group->id) }}">
                        @csrf
                        <button type="submit" class="bg-danger p-3 z-index-1 rounded-3 border-0 text-white font-xsssss text-uppercase fw-700 ls-3">Unfollow</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('groups.follow', $group->id) }}">
                        @csrf
                        <button type="submit" class="bg-current p-3 z-index-1 rounded-3 border-0 text-white font-xsssss text-uppercase fw-700 ls-3">Follow</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="card w-100 shadow-xss rounded-xxl border-0 mb-3">
            <div class="card-body d-block p-4">
                <h4 class="fw-700 mb-3 font-xsss text-grey-900">About</h4>
                <p class="fw-500 text-grey-500 lh-24 font-xssss mb-0">
                    {{ $group->description ?: 'No description available for this category yet.' }}
                </p>
            </div>
            <div class="card-body border-top-xs d-flex">
                <i class="feather-lock text-grey-500 me-3 font-lg"></i>
                <h4 class="fw-700 text-grey-900 font-xssss mt-0">Public <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">Visible to all users</span></h4>
            </div>
            <div class="card-body d-flex pt-0">
                <i class="feather-eye text-grey-500 me-3 font-lg"></i>
                <h4 class="fw-700 text-grey-900 font-xssss mt-0">Visible <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">Anyone can find this category</span></h4>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-xxl-9 col-lg-8">
        <livewire:create-post context="category" :category-id="$group->id" />
        <livewire:post-feed :category-id="$group->id" />
    </div>
</div>
@endsection
