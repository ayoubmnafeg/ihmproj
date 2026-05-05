@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="row feed-body">
    <div class="col-xl-10 col-lg-11 mx-auto">
        <div class="container py-0 py-sm-2 px-0" style="max-width: 920px;">

            <div class="welcome-hero text-center p-4 p-md-5 mb-3 mb-md-4">
                <p class="nav-caption fw-600 font-xssss text-grey-500 mb-2">Your space. Your circle.</p>
                <h1 class="font-xl fw-700 text-grey-900 mb-3" style="line-height:1.2;">
                    Stop losing updates in space chats. Put them in one <span class="text-primary">calm, private</span> feed.
                </h1>
                <p class="fw-500 text-grey-500 font-xssss lh-4 mb-4" style="max-width: 34rem; margin-left: auto; margin-right: auto;">
                    Read posts and comments for free. Create an account in minutes to post, comment, and connect with people you actually trust.
                </p>
                <ul class="list-unstyled text-start d-inline-block mb-0 px-2" style="max-width: 26rem;">
                    <li class="d-flex align-items-start font-xssss fw-500 text-grey-500 mb-2">
                        <i class="feather-check me-2 font-sm mt-1" aria-hidden="true"></i>
                        <span>See the live feed before you commit—no sign-up required to browse.</span>
                    </li>
                    <li class="d-flex align-items-start font-xssss fw-500 text-grey-500 mb-2">
                        <i class="feather-check me-2 font-sm mt-1" aria-hidden="true"></i>
                        <span>Comment, react, and post once you create a free account.</span>
                    </li>
                    <li class="d-flex align-items-start font-xssss fw-500 text-grey-500 mb-0">
                        <i class="feather-check me-2 font-sm mt-1" aria-hidden="true"></i>
                        <span>Spaces and connections so updates reach the right people.</span>
                    </li>
                </ul>
            </div>

            <div class="text-center mb-2 mb-md-3">
                <p class="font-xssss fw-600 text-grey-700 mb-3">Get started—it only takes a moment.</p>
                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-center gap-2">
                    <a
                        href="{{ route('register') }}"
                        class="d-inline-block border-0 bg-primary-gradiant p-3 rounded-3 text-white font-xssss fw-700 text-uppercase ls-1 text-decoration-none"
                        style="min-width: 200px; text-align: center;"
                    >Create a free account</a>
                    <a
                        href="{{ route('feed.index') }}"
                        class="d-inline-block welcome-cta-secondary p-3 font-xssss fw-700 text-uppercase ls-1 text-decoration-none"
                        style="min-width: 200px; text-align: center;"
                    >Preview the feed</a>
                </div>
                <p class="mt-3 mb-0 font-xssss text-grey-500">
                    Already a member? <a href="{{ route('login') }}" class="fw-600 text-primary text-decoration-none">Sign in</a>
                </p>
            </div>

            <h2 class="text-center font-xssss fw-700 text-grey-500 text-uppercase ls-1 mb-3 mt-4 mt-md-5" style="letter-spacing:0.1em;">What you can do</h2>
            <div class="row g-3 mb-2">
                <div class="col-md-4">
                    <div class="card w-100 border-0 shadow-xss rounded-xxl p-4 h-100 ui-surface-card">
                        <div class="d-inline-flex align-items-center justify-content-center bg-blue-gradiant btn-round-md mb-3" style="width: 44px; height: 44px;">
                            <i class="feather-list font-md" aria-hidden="true"></i>
                        </div>
                        <h3 class="fw-700 text-grey-900 font-xssss mb-2">Feed</h3>
                        <p class="fw-600 text-primary font-xssss mb-2">One feed. Nothing missed.</p>
                        <p class="fw-500 text-grey-500 font-xssss lh-4 mb-0">Scroll updates from your network, open threads, and read comments. Browse as a guest; join to add your voice.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card w-100 border-0 shadow-xss rounded-xxl p-4 h-100 ui-surface-card">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradiant btn-round-md mb-3" style="width: 44px; height: 44px;">
                            <i class="feather-users font-md" aria-hidden="true"></i>
                        </div>
                        <h3 class="fw-700 text-grey-900 font-xssss mb-2">Connections</h3>
                        <p class="fw-600 text-primary font-xssss mb-2">You choose who is in.</p>
                        <p class="fw-500 text-grey-500 font-xssss lh-4 mb-0">Send and accept friend requests, build a circle you trust, and keep the noise out.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card w-100 border-0 shadow-xss rounded-xxl p-4 h-100 ui-surface-card">
                        <div class="d-inline-flex align-items-center justify-content-center bg-mini-gradiant btn-round-md mb-3" style="width: 44px; height: 44px;">
                            <i class="feather-layers font-md" aria-hidden="true"></i>
                        </div>
                        <h3 class="fw-700 text-grey-900 font-xssss mb-2">Spaces</h3>
                        <p class="fw-600 text-primary font-xssss mb-2">Post where it matters.</p>
                        <p class="fw-500 text-grey-500 font-xssss lh-4 mb-0">Follow topics and post into spaces you care about, so the right people see your updates.</p>
                    </div>
                </div>
            </div>

            <div class="welcome-cta-bottom text-center p-4 p-md-5 mt-4 mt-md-5">
                <p class="font-xssss fw-700 text-uppercase mb-1 text-grey-500" style="letter-spacing:0.1em;">Ready to join?</p>
                <h2 class="font-lg fw-700 mb-4 text-grey-900">Create your account and start posting today.</h2>
                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-center gap-2">
                    <a
                        href="{{ route('register') }}"
                        class="d-inline-block border-0 bg-primary-gradiant p-3 rounded-3 text-white font-xssss fw-700 text-uppercase ls-1 text-decoration-none"
                        style="min-width: 200px; text-align: center;"
                    >Create free account</a>
                    <a
                        href="{{ route('feed.index') }}"
                        class="d-inline-block welcome-cta-secondary p-3 font-xssss fw-700 text-uppercase ls-1 text-decoration-none"
                        style="min-width: 200px; text-align: center;"
                    >Look around first</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
