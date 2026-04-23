<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sociala - @yield('title', 'Social Network')</title>
    <link rel="stylesheet" href="{{ asset('css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/emoji.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lightbox.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Montserrat:wght@300;400;500;600;700;800&display=swap">
    @livewireStyles
    @yield('styles')
    <style>
        .modal { z-index: 1000000 !important; }
        .modal-backdrop { z-index: 999999 !important; }
    </style>
</head>
<body class="color-theme-blue mont-font">

<div class="preloader"></div>

<div class="main-wrapper">

    <!-- navigation top-->
    <div class="nav-header bg-white shadow-xs border-0">
        <div class="nav-top">
            <a href="{{ route('feed.index') }}"><i class="feather-zap text-success display1-size me-2 ms-0"></i><span class="d-inline-block fredoka-font ls-3 fw-600 text-current font-xxl logo-text mb-0">{{ config('app.name') }}</span> </a>
            <a href="#" class="mob-menu ms-auto me-2 chat-active-btn"><i class="feather-message-circle text-grey-900 font-sm btn-round-md bg-greylight"></i></a>
            <a href="#" class="me-2 menu-search-icon mob-menu"><i class="feather-search text-grey-900 font-sm btn-round-md bg-greylight"></i></a>
            <button class="nav-menu me-0 ms-2"></button>
        </div>

        <form action="#" class="float-left header-search">
            <div class="form-group mb-0 icon-input">
                <i class="feather-search font-sm text-grey-400"></i>
                <input type="text" placeholder="Start typing to search.." class="bg-grey border-0 lh-32 pt-2 pb-2 ps-5 pe-3 font-xssss fw-500 rounded-xl w350 theme-dark-bg">
            </div>
        </form>

        <a href="{{ route('notifications.index') }}" class="p-2 text-center ms-auto menu-icon"><i class="feather-bell font-xl text-current"></i></a>
        <a href="{{ route('messages.index') }}" class="p-2 text-center ms-3 menu-icon chat-active-btn"><i class="feather-message-square font-xl text-current"></i></a>
        <button id="dark-mode-toggle" class="p-2 text-center ms-3 menu-icon border-0 bg-transparent cursor-pointer" title="Toggle dark mode" style="outline:none;">
            <i id="dark-mode-icon" class="feather-moon font-xl text-current"></i>
        </button>

        <div class="p-0 ms-3 menu-icon" style="position:relative;" id="profile-dropdown-wrap">
            <img src="{{ asset('images/profile-4.png') }}" alt="user" class="w40 mt--1" style="cursor:pointer;" id="profile-avatar-btn">
            <div id="profile-dropdown" style="display:none;position:absolute;top:50px;right:0;min-width:170px;z-index:9999;background:#fff;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.15);padding:8px 0;">
                <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;padding:10px 16px;font-size:13px;font-weight:600;color:#333;text-decoration:none;">
                    <i class="feather-user" style="margin-right:10px;font-size:15px;"></i> Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="display:flex;align-items:center;padding:10px 16px;font-size:13px;font-weight:600;color:#333;border:0;background:transparent;width:100%;cursor:pointer;">
                        <i class="feather-log-out" style="margin-right:10px;font-size:15px;"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- navigation top -->

    <!-- navigation left -->
    <nav class="navigation scroll-bar">
        <div class="container ps-0 pe-0">
            <div class="nav-content">
                <div class="nav-wrap bg-white bg-transparent-card rounded-xxl shadow-xss pt-3 pb-1 mb-2 mt-2">
                    <div class="nav-caption fw-600 font-xssss text-grey-500"><span>New </span>Feeds</div>
                    <ul class="mb-1 top-content">
                        <li class="logo d-none d-xl-block d-lg-block"></li>
                        <li><a href="{{ route('feed.index') }}" class="nav-content-bttn open-font"><i class="feather-tv btn-round-md bg-blue-gradiant me-3"></i><span>Newsfeed</span></a></li>
                        <li><a href="{{ route('profile.edit') }}" class="nav-content-bttn open-font"><i class="feather-user btn-round-md bg-primary-gradiant me-3"></i><span>My Profile</span></a></li>
                        <li><a href="{{ route('members.index') }}" class="nav-content-bttn open-font"><i class="feather-users btn-round-md bg-red-gradiant me-3"></i><span>Friends</span></a></li>
                        <li><a href="{{ route('groups.index') }}" class="nav-content-bttn open-font"><i class="feather-zap btn-round-md bg-mini-gradiant me-3"></i><span>Groups</span></a></li>
                    </ul>
                </div>
                @yield('left_sidebar_extras')
            </div>
        </div>
    </nav>
    <!-- navigation left -->

    <!-- main content -->
    <div class="main-content right-chat-active">
        <div class="middle-sidebar-bottom">
            <div class="middle-sidebar-left">

                @if(session('success'))
                    <div class="alert alert-success mb-3">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                @endif

                @yield('content')

            </div>
        </div>
    </div>
    <!-- main content -->

    <!-- right chat -->
    <div class="right-chat nav-wrap mt-2 right-scroll-bar">
        <div class="middle-sidebar-right-content bg-white shadow-xss rounded-xxl">

            <div class="section full pe-3 ps-4 pt-4 position-relative feed-body">
                <h4 class="font-xsssss text-grey-500 text-uppercase fw-700 ls-3">CONTACTS</h4>
                @php
                    $contacts = \App\Models\User::with('profile')
                        ->where('status', 'active')
                        ->where('id', '!=', auth()->id())
                        ->where(function ($query) {
                            $query->whereHas('sentFriendRequests', function ($requestQuery) {
                                $requestQuery->where('receiver_id', auth()->id())
                                    ->where('status', 'accepted');
                            })->orWhereHas('receivedFriendRequests', function ($requestQuery) {
                                $requestQuery->where('sender_id', auth()->id())
                                    ->where('status', 'accepted');
                            });
                        })
                        ->latest()
                        ->take(8)
                        ->get();
                @endphp
                <ul class="list-group list-group-flush">
                    @forelse($contacts as $contact)
                        <li class="bg-transparent list-group-item no-icon pe-0 ps-0 pt-2 pb-2 border-0 d-flex align-items-center">
                            <figure class="avatar float-left mb-0 me-2">
                                <img src="{{ $contact->profile->avatar_url ?: asset('images/user-12.png') }}" alt="image" class="w35">
                            </figure>
                            <h3 class="fw-700 mb-0 mt-0">
                                <a class="font-xssss text-grey-600 d-block text-dark" href="{{ route('profile.show', $contact->id) }}">
                                    {{ $contact->profile->display_name ?: ('anon_' . $contact->id) }}
                                </a>
                            </h3>
                            <span class="bg-success ms-auto btn-round-xss"></span>
                        </li>
                    @empty
                        <li class="bg-transparent list-group-item no-icon pe-0 ps-0 pt-2 pb-2 border-0">
                            <span class="font-xssss text-grey-500">No friends to show yet.</span>
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="section full pe-3 ps-4 pt-4 pb-4 position-relative feed-body">
                <h4 class="font-xsssss text-grey-500 text-uppercase fw-700 ls-3">GROUPS</h4>
                <ul class="list-group list-group-flush">
                    <li class="bg-transparent list-group-item no-icon pe-0 ps-0 pt-2 pb-2 border-0 d-flex align-items-center">
                        <span class="btn-round-sm bg-primary-gradiant me-3 ls-3 text-white font-xssss fw-700">UD</span>
                        <h3 class="fw-700 mb-0 mt-0"><a class="font-xssss text-grey-600 d-block text-dark model-popup-chat" href="#">Studio Express</a></h3>
                        <span class="badge mt-0 text-grey-500 badge-pill pe-0 font-xsssss">2 min</span>
                    </li>
                    <li class="bg-transparent list-group-item no-icon pe-0 ps-0 pt-2 pb-2 border-0 d-flex align-items-center">
                        <span class="btn-round-sm bg-gold-gradiant me-3 ls-3 text-white font-xssss fw-700">AR</span>
                        <h3 class="fw-700 mb-0 mt-0"><a class="font-xssss text-grey-600 d-block text-dark model-popup-chat" href="#">Armany Design</a></h3>
                        <span class="bg-warning ms-auto btn-round-xss"></span>
                    </li>
                    <li class="bg-transparent list-group-item no-icon pe-0 ps-0 pt-2 pb-2 border-0 d-flex align-items-center">
                        <span class="btn-round-sm bg-mini-gradiant me-3 ls-3 text-white font-xssss fw-700">UD</span>
                        <h3 class="fw-700 mb-0 mt-0"><a class="font-xssss text-grey-600 d-block text-dark model-popup-chat" href="#">De fabous</a></h3>
                        <span class="bg-success ms-auto btn-round-xss"></span>
                    </li>
                </ul>
            </div>

            <div class="section full pe-3 ps-4 pt-0 pb-4 position-relative feed-body">
                <h4 class="font-xsssss text-grey-500 text-uppercase fw-700 ls-3">Pages</h4>
                <ul class="list-group list-group-flush">
                    <li class="bg-transparent list-group-item no-icon pe-0 ps-0 pt-2 pb-2 border-0 d-flex align-items-center">
                        <span class="btn-round-sm bg-primary-gradiant me-3 ls-3 text-white font-xssss fw-700">AB</span>
                        <h3 class="fw-700 mb-0 mt-0"><a class="font-xssss text-grey-600 d-block text-dark model-popup-chat" href="#">Studio Express</a></h3>
                        <span class="bg-success ms-auto btn-round-xss"></span>
                    </li>
                    <li class="bg-transparent list-group-item no-icon pe-0 ps-0 pt-2 pb-2 border-0 d-flex align-items-center">
                        <span class="btn-round-sm bg-gold-gradiant me-3 ls-3 text-white font-xssss fw-700">SD</span>
                        <h3 class="fw-700 mb-0 mt-0"><a class="font-xssss text-grey-600 d-block text-dark model-popup-chat" href="#">Armany Seary</a></h3>
                        <span class="bg-success ms-auto btn-round-xss"></span>
                    </li>
                    <li class="bg-transparent list-group-item no-icon pe-0 ps-0 pt-2 pb-2 border-0 d-flex align-items-center">
                        <span class="btn-round-sm bg-gold-gradiant me-3 ls-3 text-white font-xssss fw-700">SD</span>
                        <h3 class="fw-700 mb-0 mt-0"><a class="font-xssss text-grey-600 d-block text-dark model-popup-chat" href="#">Entropio Inc</a></h3>
                        <span class="bg-success ms-auto btn-round-xss"></span>
                    </li>
                </ul>
            </div>

        </div>
    </div>
    <!-- right chat -->

    <div class="app-footer border-0 shadow-lg bg-primary-gradiant">
        <a href="{{ route('feed.index') }}" class="nav-content-bttn nav-center"><i class="feather-home"></i></a>
        <a href="#" class="nav-content-bttn"><i class="feather-package"></i></a>
        <a href="#" class="nav-content-bttn" data-tab="chats"><i class="feather-layout"></i></a>
        <a href="#" class="nav-content-bttn"><i class="feather-layers"></i></a>
        <a href="{{ route('settings.index') }}" class="nav-content-bttn"><img src="{{ asset('images/profile-4.png') }}" alt="user" class="w30 shadow-xss"></a>
    </div>

    <div class="app-header-search">
        <form class="search-form">
            <div class="form-group searchbox mb-0 border-0 p-1">
                <input type="text" class="form-control border-0" placeholder="Search...">
                <i class="input-icon">
                    <ion-icon name="search-outline" role="img" class="md hydrated" aria-label="search outline"></ion-icon>
                </i>
                <a href="#" class="ms-1 mt-1 d-inline-block close searchbox-close">
                    <i class="ti-close font-xs"></i>
                </a>
            </div>
        </form>
    </div>

</div>

<div class="modal bottom side fade" id="Modalstory" tabindex="-1" role="dialog" style="overflow-y: auto;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 bg-transparent">
            <button type="button" class="close mt-0 position-absolute top--30 right--10" data-dismiss="modal" aria-label="Close"><i class="ti-close text-grey-900 font-xssss"></i></button>
            <div class="modal-body p-0">
                <div class="card w-100 border-0 rounded-3 overflow-hidden bg-gradiant-bottom bg-gradiant-top">
                    <div class="owl-carousel owl-theme dot-style3 story-slider owl-dot-nav nav-none">
                        <div class="item"><img src="{{ asset('images/story-5.jpg') }}" alt="image"></div>
                        <div class="item"><img src="{{ asset('images/story-6.jpg') }}" alt="image"></div>
                        <div class="item"><img src="{{ asset('images/story-7.jpg') }}" alt="image"></div>
                        <div class="item"><img src="{{ asset('images/story-8.jpg') }}" alt="image"></div>
                    </div>
                </div>
                <div class="form-group mt-3 mb-0 p-3 position-absolute bottom-0 z-index-1 w-100">
                    <input type="text" class="style2-input w-100 bg-transparent border-light-md p-3 pe-5 font-xssss fw-500 text-white" value="Write Comments">
                    <span class="feather-send text-white font-md text-white position-absolute" style="bottom: 35px;right:30px;"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-popup-chat">
    <div class="modal-popup-wrap bg-white p-0 shadow-lg rounded-3">
        <div class="modal-popup-header w-100 border-bottom">
            <div class="card p-3 d-block border-0 d-block">
                <figure class="avatar mb-0 float-left me-2"><img src="{{ asset('images/user-12.png') }}" alt="image" class="w35 me-1"></figure>
                <h5 class="fw-700 text-primary font-xssss mt-1 mb-1">Hendrix Stamp</h5>
                <h4 class="text-grey-500 font-xsssss mt-0 mb-0"><span class="d-inline-block bg-success btn-round-xss m-0"></span> Available</h4>
                <a href="#" class="font-xssss position-absolute right-0 top-0 mt-3 me-4"><i class="ti-close text-grey-900 mt-2 d-inline-block"></i></a>
            </div>
        </div>
        <div class="modal-popup-body w-100 p-3 h-auto">
            <div class="message"><div class="message-content font-xssss lh-24 fw-500">Hi, how can I help you?</div></div>
            <div class="date-break font-xsssss lh-24 fw-500 text-grey-500 mt-2 mb-2">Mon 10:20am</div>
            <div class="message self text-right mt-2"><div class="message-content font-xssss lh-24 fw-500">I want those files for you. I want you to send 1 PDF and 1 image file.</div></div>
            <div class="snippet pt-3 ps-4 pb-2 pe-3 mt-2 bg-grey rounded-xl float-right" data-title=".dot-typing"><div class="stage"><div class="dot-typing"></div></div></div>
            <div class="clearfix"></div>
        </div>
        <div class="modal-popup-footer w-100 border-top">
            <div class="card p-3 d-block border-0 d-block">
                <div class="form-group icon-right-input style1-input mb-0"><input type="text" placeholder="Start typing.." class="form-control rounded-xl bg-greylight border-0 font-xssss fw-500 ps-3"><i class="feather-send text-grey-500 font-md"></i></div>
            </div>
        </div>
    </div>
</div>

@stack('modals')

<script src="{{ asset('js/plugin.js') }}"></script>
<script src="{{ asset('js/scripts.js') }}"></script>
@livewireScripts
<script>
(function () {
    var btn = document.getElementById('profile-avatar-btn');
    var dropdown = document.getElementById('profile-dropdown');
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    });
    document.addEventListener('click', function () {
        dropdown.style.display = 'none';
    });
})();

(function () {
    var toggle = document.getElementById('dark-mode-toggle');
    var icon = document.getElementById('dark-mode-icon');

    function applyDark(dark) {
        if (dark) {
            document.body.classList.add('theme-dark');
        } else {
            document.body.classList.remove('theme-dark');
        }
        icon.className = dark ? 'feather-sun font-xl text-current' : 'feather-moon font-xl text-current';
        localStorage.setItem('darkMode', dark ? '1' : '0');
    }

    if (localStorage.getItem('darkMode') === '1') applyDark(true);

    toggle.addEventListener('click', function () {
        applyDark(!document.body.classList.contains('theme-dark'));
    });
})();
</script>
@yield('scripts')
</body>
</html>
