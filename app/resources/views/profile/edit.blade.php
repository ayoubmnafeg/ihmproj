@extends('layouts.app')

@section('title', 'Edit Anonymous Profile')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100 border-0 p-0 bg-white shadow-xss rounded-xxl">
            <div class="card-body h250 p-0 rounded-xxl overflow-hidden m-3">
                <img src="{{ asset('images/u-bg.jpg') }}" alt="image">
            </div>
            <div class="card-body p-0 position-relative">
                <figure class="avatar position-absolute w100 z-index-1" style="top:-40px; left: 30px;">
                    <img src="{{ asset('images/user-12.png') }}" alt="avatar" class="float-right p-1 bg-white rounded-circle w-100">
                </figure>
                <h4 class="fw-700 font-sm mt-2 mb-lg-5 mb-4 pl-15">
                    {{ $user->profile->display_name ?: ('anon_' . $user->id) }}
                </h4>
                <div class="d-flex align-items-center justify-content-center position-absolute-md right-15 top-0 me-2">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#editProfileModal" class="d-none d-lg-block bg-primary-gradiant p-3 z-index-1 rounded-3 text-white font-xsssss text-uppercase fw-700 ls-3 border-0">
                        Edit Profile
                    </button>
                    <a href="#" class="d-none d-lg-block bg-greylight btn-round-lg ms-2 rounded-3 text-grey-700"><i class="feather-shield font-md"></i></a>
                    <a href="#" class="d-none d-lg-block bg-greylight btn-round-lg ms-2 rounded-3 text-grey-700"><i class="ti-more font-md text-dark"></i></a>
                </div>
            </div>

            <div class="card-body d-block w-100 shadow-none mb-0 p-0 border-top-xs"></div>
        </div>
    </div>
    <div class="col-xl-4 col-xxl-3 col-lg-4 pe-0">
        <div class="card w-100 shadow-xss rounded-xxl border-0 mb-3 mt-3">
            <div class="card-body border-top-xs d-flex">
                <i class="feather-lock text-grey-500 me-3 font-lg"></i>
                <h4 class="fw-700 text-grey-900 font-xssss mt-0">Private by default <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">New uploads are hidden until you publish.</span></h4>
            </div>

            <div class="card-body d-flex pt-0">
                <i class="feather-eye text-grey-500 me-3 font-lg"></i>
                <h4 class="fw-700 text-grey-900 font-xssss mt-0">Discoverability <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">Only alias appears in search and mentions.</span></h4>
            </div>
            <div class="card-body d-flex pt-0">
                <i class="feather-shield text-grey-500 me-3 font-lg"></i>
                <h4 class="fw-700 text-grey-900 font-xssss mt-1">Safety mode enabled</h4>
            </div>
            <div class="card-body d-flex pt-0">
                <i class="feather-alert-triangle text-grey-500 me-3 font-lg"></i>
                <h4 class="fw-700 text-grey-900 font-xssss mt-1">Auto-hide sensitive previews</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-8 col-xxl-9 col-lg-8">
        <livewire:create-post context="profile" modal-id="createPostModalProfile" />
        <livewire:post-feed scope="mine" />
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-xxl border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-700 font-md text-grey-900" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="display_name" class="form-label fw-600 text-grey-900 font-xssss">Alias</label>
                        <input
                            id="display_name"
                            type="text"
                            name="display_name"
                            maxlength="255"
                            value="{{ old('display_name', $user->profile->display_name ?? '') }}"
                            class="form-control rounded-xl bg-greylight border-0 font-xssss fw-500 @error('display_name') is-invalid @enderror"
                            placeholder="anon_creator"
                        >
                        @error('display_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label fw-600 text-grey-900 font-xssss">Bio</label>
                        <textarea
                            id="bio"
                            name="bio"
                            rows="4"
                            class="form-control rounded-xl bg-greylight border-0 font-xssss fw-500 @error('bio') is-invalid @enderror"
                            placeholder="Share your vibe without sharing your identity."
                        >{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="avatar_url" class="form-label fw-600 text-grey-900 font-xssss">Avatar URL</label>
                        <input
                            id="avatar_url"
                            type="url"
                            name="avatar_url"
                            value="{{ old('avatar_url', $user->profile->avatar_url ?? '') }}"
                            class="form-control rounded-xl bg-greylight border-0 font-xssss fw-500 @error('avatar_url') is-invalid @enderror"
                            placeholder="https://..."
                        >
                        @error('avatar_url')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="w-100 p-2 lh-20 bg-primary-gradiant text-white text-center font-xssss fw-600 ls-1 rounded-xl border-0">
                        Save Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

