@extends('layouts.app')

@section('title', __('Search results'))

@section('content')
<div class="row feed-body">
    <div class="col-xl-10 col-lg-10 mx-auto">
        @if($q === '')
            <div class="card border-0 shadow-xss rounded-xxl p-4 mb-3 bg-white">
                <h1 class="fw-700 font-md text-grey-900 mb-1">{{ __('Search results') }}</h1>
                <p class="font-xssss text-grey-500 mb-3">{{ __('Enter a search term to see posts and spaces.') }}</p>
                <p class="font-xsss text-grey-500 mb-0 text-center">{{ __('Use the search bar to find posts and spaces.') }}</p>
            </div>
        @elseif(! $hasPostHits && $spaces->isEmpty() && $people->isEmpty())
            <div class="card border-0 shadow-xss rounded-xxl p-4 mb-3 bg-white">
                <h1 class="fw-700 font-md text-grey-900 mb-1">{{ __('Search results') }}</h1>
                <p class="font-xssss text-grey-500 mb-3">{{ __('Showing results for') }} <span class="text-grey-900 fw-600">“{{ $q }}”</span></p>
                <p class="font-xsss text-grey-500 mb-0 text-center">{{ __('No search results') }}</p>
            </div>
        @else
            <div class="card border-0 shadow-xss rounded-xxl mb-3 bg-white search-results-tabs">
                <div class="px-4 pt-4 pb-2">
                    <h1 class="fw-700 font-md text-grey-900 mb-1">{{ __('Search results') }}</h1>
                    <p class="font-xssss text-grey-500 mb-0">{{ __('Showing results for') }} <span class="text-grey-900 fw-600">“{{ $q }}”</span></p>
                </div>

                <ul class="nav nav-tabs search-results-tabs__nav px-4 border-0" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link active fw-600 font-xsss"
                            id="search-tab-posts"
                            data-bs-toggle="tab"
                            data-bs-target="#search-pane-posts"
                            type="button"
                            role="tab"
                            aria-controls="search-pane-posts"
                            aria-selected="true"
                        >{{ __('Posts') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link fw-600 font-xsss"
                            id="search-tab-spaces"
                            data-bs-toggle="tab"
                            data-bs-target="#search-pane-spaces"
                            type="button"
                            role="tab"
                            aria-controls="search-pane-spaces"
                            aria-selected="false"
                        >{{ __('Spaces') }}</button>
                    </li>
                </ul>

                <div class="tab-content search-results-tabs__content px-4 pb-4 pt-2">
                    <div class="tab-pane fade show active" id="search-pane-posts" role="tabpanel" aria-labelledby="search-tab-posts" tabindex="0">
                        <livewire:post-feed scope="search" :search-query="$q" :bare-surface="true" wire:key="search-posts-{{ md5($q) }}" />
                    </div>
                    <div class="tab-pane fade" id="search-pane-spaces" role="tabpanel" aria-labelledby="search-tab-spaces" tabindex="0">
                        @if($spaces->isNotEmpty())
                            <ul class="list-unstyled mb-4">
                                @foreach($spaces as $space)
                                    <li class="py-2">
                                        <a href="{{ route('groups.show', $space->id) }}" class="fw-600 font-xsss text-grey-900 text-decoration-none">
                                            {{ $space->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if($people->isNotEmpty())
                            <p class="font-xsssss text-grey-500 text-uppercase fw-700 ls-3 mb-2">{{ __('People') }}</p>
                            <ul class="list-unstyled">
                                @foreach($people as $person)
                                    <li class="py-2 d-flex align-items-center">
                                        <img src="{{ $person->profile?->avatar_url ?: asset('images/user-12.png') }}" alt="" width="36" height="36" class="rounded-circle me-2">
                                        <a href="{{ route('profile.show', $person->id) }}" class="fw-600 font-xsss text-grey-900 text-decoration-none">
                                            {{ $person->profile?->display_name ?? ('anon_'.$person->id) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if($spaces->isEmpty() && $people->isEmpty())
                            <p class="fw-500 text-grey-500 font-xssss mb-0 text-center py-4">{{ __('No spaces or people match your search.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
