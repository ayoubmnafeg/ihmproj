@extends('layouts.app')

@section('title', __('Notifications'))

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card shadow-xss w-100 d-block border-0 p-4 mb-3">
            <div class="card-body p-0">
                <h2 class="fw-700 mb-3 font-md text-grey-900">{{ __('Notifications') }}</h2>
                <p class="fw-500 font-xsss text-grey-500 mb-0">{{ __('No notifications yet.') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
