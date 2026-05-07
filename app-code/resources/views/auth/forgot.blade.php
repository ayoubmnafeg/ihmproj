@extends('layouts.auth')

@section('title', __('Forgot Password'))

@section('content')
<div class="card shadow-none border-0 auth-form-slide-card">
    <div class="card-body rounded-0 text-left px-1 px-sm-2">
        <h2 class="fw-700 display1-size display2-md-size mb-3 header-title">{{ __('Reset your password') }}</h2>
        <p class="fw-500 font-xsss text-grey-500 mb-4">
            {{ __('Contact support or use the sign-in page if you already have access.') }}
        </p>
        <a href="{{ route('login') }}" class="fw-600 font-xsss text-primary">{{ __('Back to login') }}</a>
    </div>
</div>
@endsection
