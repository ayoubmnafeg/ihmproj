@extends('layouts.guest')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 text-center default-page min-vh-100 align-items-center d-flex">
            <div class="card border-0 text-center d-block p-0 w-100 bg-transparent">
                <img src="{{ asset('images/coming-soon.png') }}" alt="404 icon" class="w200 mb-4 ms-auto me-auto pt-md-5">
                <h1 class="fw-700 text-grey-900 display3-size display4-md-size">Oops! It looks like you're lost.</h1>
                <p class="text-grey-500 font-xsss">
                    The page you're looking for isn't available. Try to search again or go back home.
                </p>
                <a href="{{ route('feed.index') }}" class="p-3 w175 bg-current text-white d-inline-block text-center fw-600 font-xssss rounded-3 text-uppercase ls-3">
                    Home Page
                </a>
            </div>
        </div>
    </div>
@endsection
