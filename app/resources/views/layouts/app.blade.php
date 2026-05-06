<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/emoji.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lightbox.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/theme-private.css') }}">
    @livewireStyles
    @yield('styles')
    <style>
        .modal { z-index: 1000000 !important; }
        .modal-backdrop { z-index: 999999 !important; }
        #notification-menu {
            width: 320px;
            min-width: 320px;
            max-width: 90vw;
        }
        #notification-menu .card {
            padding-left: 52px !important;
        }
        #notification-menu .card h5 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #notification-menu .card h5 span {
            float: none !important;
            flex-shrink: 0;
        }
        #notification-menu .card h6 {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0;
        }
        .lang-flag-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.08);
        }
        body.theme-dark .lang-flag-icon {
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.14);
        }
    </style>
</head>
<body class="ui-private color-theme-blue mont-font">

<div class="preloader"></div>

<div class="main-wrapper">
    @php
        $langFlagEn = asset('images/lang-en.png');
        $langFlagFr = asset('images/lang-fr.png');
        $langFlagCurrent = app()->getLocale() === 'fr' ? $langFlagFr : $langFlagEn;
    @endphp

    <!-- navigation top + accent strip -->
    <header class="app-header-chrome">
    <div class="nav-header bg-white shadow-xs border-0">
        <div class="nav-top d-flex align-items-center">
            <div class="nav-header-sidebar-cluster d-flex align-items-center min-w-0">
                <a
                    href="{{ auth()->check() ? route('feed.index') : route('home') }}"
                    class="text-decoration-none d-inline-flex align-items-center flex-shrink-0 app-brand-link"
                    title="{{ config('app.name') }}"
                    aria-label="{{ config('app.name') }}"
                >
                    <i class="feather-shield text-grey-600" style="font-size:1.5rem;" aria-hidden="true"></i>
                </a>
                <form action="#" class="header-search sidebar-header-search mb-0 min-w-0" onsubmit="return false;">
                    <div class="form-group mb-0 icon-input">
                        <i class="feather-search font-sm text-grey-400"></i>
                        <input
                            type="text"
                            placeholder="{{ __('Search...') }}"
                            class="bg-grey border-0 lh-32 pt-2 pb-2 ps-5 pe-3 font-xssss fw-500 rounded-xl w-100 theme-dark-bg sidebar-header-search-input"
                            readonly
                            autocomplete="off"
                            aria-haspopup="dialog"
                            aria-controls="appSearchModal"
                        >
                    </div>
                </form>
            </div>
            <a href="#" class="mob-menu ms-auto me-2 chat-active-btn header-tool header-tool--mob"><i class="feather-message-circle font-sm"></i></a>
            <a href="#" class="me-2 mob-menu header-tool header-tool--mob" data-app-search-modal title="{{ __('Search the app') }}" aria-label="{{ __('Search the app') }}"><i class="feather-search font-sm"></i></a>
            <button class="nav-menu me-0 ms-2"></button>
        </div>

        @auth
        <div class="nav-header-actions d-flex align-items-center flex-shrink-0 ms-auto gap-2 flex-wrap">
        <div class="dropdown p-2 text-center menu-icon d-inline-block">
            <a href="#" class="position-relative d-inline-block header-tool text-decoration-none" id="dropdownMenu3" data-bs-toggle="dropdown" aria-expanded="false" title="Activity">
                <span class="dot-count header-notify-badge" aria-hidden="true"></span>
                <i class="feather-bell font-xl" aria-hidden="true"></i>
            </a>
            <div id="notification-menu" class="dropdown-menu dropdown-menu-end p-4 rounded-3 border-0 shadow-lg" aria-labelledby="dropdownMenu3">
                <h4 class="fw-700 font-xss mb-4">{{ __('Notification') }}</h4>
                <div class="card bg-transparent-card w-100 border-0 ps-5 mb-3">
                    <img src="{{ asset('images/user-8.png') }}" alt="user" class="w40 position-absolute left-0">
                    <h5 class="font-xsss text-grey-900 mb-1 mt-0 fw-700 d-block">Hendrix Stamp <span class="text-grey-400 font-xsssss fw-600 float-right mt-1"> 3 min</span></h5>
                    <h6 class="text-grey-500 fw-500 font-xssss lh-4">There are many variations of pass..</h6>
                </div>
                <div class="card bg-transparent-card w-100 border-0 ps-5 mb-3">
                    <img src="{{ asset('images/user-4.png') }}" alt="user" class="w40 position-absolute left-0">
                    <h5 class="font-xsss text-grey-900 mb-1 mt-0 fw-700 d-block">Goria Coast <span class="text-grey-400 font-xsssss fw-600 float-right mt-1"> 2 min</span></h5>
                    <h6 class="text-grey-500 fw-500 font-xssss lh-4">Mobile Apps UI Designer is require..</h6>
                </div>
                <div class="card bg-transparent-card w-100 border-0 ps-5 mb-3">
                    <img src="{{ asset('images/user-7.png') }}" alt="user" class="w40 position-absolute left-0">
                    <h5 class="font-xsss text-grey-900 mb-1 mt-0 fw-700 d-block">Surfiya Zakir <span class="text-grey-400 font-xsssss fw-600 float-right mt-1"> 1 min</span></h5>
                    <h6 class="text-grey-500 fw-500 font-xssss lh-4">Mobile Apps UI Designer is require..</h6>
                </div>
                <div class="card bg-transparent-card w-100 border-0 ps-5">
                    <img src="{{ asset('images/user-6.png') }}" alt="user" class="w40 position-absolute left-0">
                    <h5 class="font-xsss text-grey-900 mb-1 mt-0 fw-700 d-block">Victor Exrixon <span class="text-grey-400 font-xsssss fw-600 float-right mt-1"> 30 sec</span></h5>
                    <h6 class="text-grey-500 fw-500 font-xssss lh-4">Mobile Apps UI Designer is require..</h6>
                </div>
            </div>
        </div>
        <a href="{{ route('messages.index') }}" class="p-2 text-center menu-icon chat-active-btn header-tool text-decoration-none" title="Messages"><i class="feather-message-square font-xl" aria-hidden="true"></i></a>
        <div class="p-0 menu-icon header-tool-wrap" style="position:relative;" id="profile-dropdown-wrap">
            <button type="button" class="header-account-btn" id="profile-avatar-btn" title="Account" aria-label="Open account menu" aria-haspopup="true" aria-expanded="false">
                <i class="feather-user" aria-hidden="true"></i>
            </button>
            <div id="profile-dropdown" class="profile-menu-dropdown bg-white shadow-sm rounded-3 border-0" style="display:none;position:absolute;top:50px;right:0;min-width:200px;z-index:9999;padding:8px 0;overflow:visible;">
                <div id="profile-lang-wrap" style="position:relative;width:100%;">
                    <button type="button" id="profile-lang-submenu-btn" class="text-dark w-100 border-0 bg-transparent cursor-pointer header-tool" aria-expanded="false" aria-haspopup="true" aria-controls="profile-lang-submenu" title="{{ __('Language') }}" style="display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:13px;font-weight:600;outline:none;text-align:left;">
                        <img src="{{ $langFlagCurrent }}" alt="" width="22" height="22" class="lang-flag-icon">
                        <span style="flex:1;">{{ __('Language') }}</span>
                        <span class="text-grey-500 font-xsssss fw-700">{{ strtoupper(app()->getLocale() == 'fr' ? 'fr' : 'en') }}</span>
                        <i class="feather-chevron-right profile-lang-submenu-chevron text-grey-500" style="font-size:14px;opacity:0.75;flex-shrink:0;" aria-hidden="true"></i>
                    </button>
                    <div id="profile-lang-submenu" class="bg-white rounded-3 border-0" role="menu" aria-labelledby="profile-lang-submenu-btn" style="display:none;position:absolute;right:100%;top:0;margin-right:8px;min-width:160px;padding:8px 0;z-index:10050;box-shadow:0 10px 40px rgba(0,0,0,.14);">
                        <a href="{{ route('lang.switch', 'en') }}" class="text-dark text-decoration-none" role="menuitem" style="display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:13px;font-weight:600;">
                            <img src="{{ $langFlagEn }}" alt="" width="22" height="22" class="lang-flag-icon">{{ __('English') }}
                        </a>
                        <a href="{{ route('lang.switch', 'fr') }}" class="text-dark text-decoration-none" role="menuitem" style="display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:13px;font-weight:600;">
                            <img src="{{ $langFlagFr }}" alt="" width="22" height="22" class="lang-flag-icon">{{ __('Français') }}
                        </a>
                    </div>
                </div>
                <button type="button" class="dark-mode-toggle text-dark w-100 border-0 bg-transparent cursor-pointer header-tool" title="{{ __('Lower luminance (easier in dim light)') }}" aria-label="{{ __('Toggle theme') }}" style="display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:13px;font-weight:600;outline:none;text-align:left;">
                    <i class="dark-mode-icon feather-moon font-xl" style="opacity:0.85;flex-shrink:0;" aria-hidden="true"></i><span style="flex:1;">{{ __('Toggle theme') }}</span>
                </button>
                <div style="border-top:1px solid #eee;margin:4px 0 0;padding-top:4px;" role="separator"></div>
                <a href="{{ route('profile.edit') }}" class="text-dark" style="display:flex;align-items:center;padding:10px 16px;font-size:13px;font-weight:600;text-decoration:none;">
                    <i class="feather-sliders" style="margin-right:10px;font-size:15px;opacity:0.85;"></i> {{ __('Account & privacy') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-dark" style="display:flex;align-items:center;padding:10px 16px;font-size:13px;font-weight:600;border:0;background:transparent;width:100%;cursor:pointer;text-align:left;">
                        <i class="feather-log-out" style="margin-right:10px;font-size:15px;opacity:0.85;"></i> {{ __('Sign out') }}
                    </button>
                </form>
            </div>
        </div>
        </div>
        @else
        <div class="d-flex align-items-center ms-auto gap-2 flex-wrap">
            <a
                href="{{ route('login') }}"
                class="d-inline-flex align-items-center justify-content-center text-decoration-none font-xssss fw-600 header-guest-btn header-guest-btn--signin rounded-xl"
            >{{ __('Sign in') }}</a>
            <a
                href="{{ route('register') }}"
                class="d-inline-flex align-items-center justify-content-center text-decoration-none font-xssss fw-600 border-0 header-guest-btn header-guest-btn--register bg-primary-gradiant rounded-xl"
            >{{ __('Create account') }}</a>
        </div>
        @endauth

        @guest
        <div class="nav-header-actions d-flex align-items-center flex-shrink-0 gap-2">
        <div class="dropdown p-2 text-center menu-icon d-inline-block">
            <a href="#" class="position-relative d-inline-flex align-items-center header-tool text-decoration-none text-dark fw-600 font-xssss pt-1 pb-1 ps-2 pe-2 rounded-xl" id="langMenu" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Language') }}" style="border:1px solid #eee;">
                <img src="{{ $langFlagCurrent }}" alt="" width="22" height="22" class="lang-flag-icon me-1">
                {{ strtoupper(app()->getLocale() == 'fr' ? 'fr' : 'en') }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="langMenu" style="z-index: 100000; position: absolute;">
                <li><a class="dropdown-item d-flex align-items-center gap-2 fw-600 font-xsss" href="{{ route('lang.switch', 'en') }}"><img src="{{ $langFlagEn }}" alt="" width="22" height="22" class="lang-flag-icon">{{ __('English') }}</a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2 fw-600 font-xsss" href="{{ route('lang.switch', 'fr') }}"><img src="{{ $langFlagFr }}" alt="" width="22" height="22" class="lang-flag-icon">{{ __('Français') }}</a></li>
            </ul>
        </div>
        <button type="button" class="dark-mode-toggle p-2 text-center menu-icon border-0 bg-transparent cursor-pointer header-tool" title="{{ __('Lower luminance (easier in dim light)') }}" aria-label="{{ __('Toggle theme') }}" style="outline:none;">
            <i class="dark-mode-icon feather-moon font-xl" aria-hidden="true"></i>
        </button>
        </div>
        @endguest
    </div>
    <div class="app-header-subbar" role="presentation" aria-hidden="true"></div>
    </header>

    <!-- navigation left -->
    <nav id="app-left-navigation" class="navigation scroll-bar" aria-label="{{ __('Main navigation') }}">
        <button
            type="button"
            id="left-sidebar-edge-toggle"
            class="left-sidebar-edge-toggle"
            aria-expanded="true"
            aria-controls="app-left-navigation"
            title="{{ __('Collapse sidebar') }}"
        >
            <i class="feather-menu" aria-hidden="true"></i>
        </button>
        <div class="container ps-0 pe-0">
            <div class="nav-content">
                <div class="nav-wrap bg-white bg-transparent-card rounded-xxl shadow-xss pt-3 pb-1 mb-2 mt-2">
                    <ul class="mb-1 top-content">
                        <li class="logo d-none d-xl-block d-lg-block"></li>
                        @guest
                        <li>
                            <a
                                href="{{ route('welcome') }}"
                                @class(['nav-content-bttn', 'open-font', 'fw-600' => request()->routeIs('welcome') || (request()->routeIs('home') && ! auth()->check())])
                                title="What this app is for"
                            >
                                <i class="feather-info btn-round-md bg-current me-3" style="box-shadow: none;"></i><span>{{ __('Welcome') }}</span>
                            </a>
                        </li>
                        @endguest
                        <li><a href="{{ route('feed.index') }}" class="nav-content-bttn open-font" title="Latest activity from your network"><i class="feather-list btn-round-md bg-blue-gradiant me-3"></i><span>{{ __('Feed') }}</span></a></li>
                        @auth
                        <li><a href="{{ route('profile.edit') }}" class="nav-content-bttn open-font"><i class="feather-user btn-round-md bg-primary-gradiant me-3"></i><span>{{ __('Account') }}</span></a></li>
                        <li><a href="{{ route('members.index') }}" class="nav-content-bttn open-font"><i class="feather-users btn-round-md bg-red-gradiant me-3"></i><span>{{ __('Connections') }}</span></a></li>
                        @endauth
                    </ul>
                </div>
                @auth
                    @php
                        $sidebarSpacesMeta = auth()->user()
                            ->followedCategories()
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->get()
                            ->map(function ($c) {
                                $compact = preg_replace('/\s+/u', '', $c->name ?? '') ?: '';

                                return [
                                    'id' => $c->id,
                                    'name' => $c->name,
                                    'initials' => strtoupper(\Illuminate\Support\Str::substr($compact, 0, 2) ?: '__'),
                                    'avatar' => $c->profile_image_path ? asset('storage/'.$c->profile_image_path) : null,
                                    'url' => route('groups.show', $c->id),
                                ];
                            });
                    @endphp
                    <div
                        id="sidebar-spaces-stack"
                        class="sidebar-spaces-stack px-3 pb-2"
                        data-user-id="{{ auth()->id() }}"
                    >
                        <script type="application/json" id="sidebar-spaces-bootstrap">{!! json_encode($sidebarSpacesMeta->values()->all(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

                        <div class="sidebar-spaces-panel sidebar-spaces-panel--spaces mb-3">
                            <button
                                type="button"
                                class="sidebar-spaces-heading"
                                aria-expanded="true"
                                aria-controls="sidebar-all-spaces-body"
                                id="sidebar-spaces-section-heading"
                            >
                                <span class="sidebar-spaces-heading-label">{{ __('Spaces') }}</span>
                                <i class="feather-chevron-down sidebar-spaces-chevron" aria-hidden="true"></i>
                            </button>
                            <div class="sidebar-spaces-body pt-2" id="sidebar-all-spaces-body" role="region" aria-labelledby="sidebar-spaces-section-heading">
                                <a href="{{ route('groups.index', ['following' => true]) }}" class="sidebar-spaces-manage font-xsss fw-600">
                                    <i class="feather-settings" aria-hidden="true"></i><span>{{ __('Manage spaces') }}</span>
                                </a>
                                <div id="sidebar-all-spaces-list" class="sidebar-spaces-rows">
                                    @forelse($sidebarSpacesMeta as $space)
                                        <div class="sidebar-space-row" data-space-id="{{ $space['id'] }}">
                                            <a href="{{ $space['url'] }}" class="sidebar-space-link">
                                                @if($space['avatar'])
                                                    <img src="{{ $space['avatar'] }}" alt="" width="28" height="28" class="sidebar-space-avatar sidebar-space-avatar-img rounded-circle shadow-sm">
                                                @else
                                                    <span class="sidebar-space-avatar sidebar-space-avatar-label rounded-circle">{{ $space['initials'] }}</span>
                                                @endif
                                                <span class="sidebar-space-name text-truncate font-xsss fw-600">{{ $space['name'] }}</span>
                                            </a>
                                            <button
                                                type="button"
                                                class="sidebar-space-star-btn"
                                                data-space-id="{{ $space['id'] }}"
                                                aria-pressed="false"
                                                title="{{ __('Favorite') }}"
                                            ><i class="feather-star" aria-hidden="true"></i></button>
                                        </div>
                                    @empty
                                        <p class="sidebar-spaces-muted font-xsss fw-600 mb-0 pt-2 pb-2">{{ __('You are not following any spaces yet.') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth
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

            @auth
            @yield('right_sidebar_extra')

            <div class="section full pe-3 ps-4 pt-4 position-relative feed-body">
                <h4 class="font-xsssss text-grey-500 text-uppercase fw-700 ls-3">{{ __('People you trust') }}</h4>
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
                        </li>
                    @empty
                        <li class="bg-transparent list-group-item no-icon pe-0 ps-0 pt-2 pb-2 border-0">
                            <span class="font-xssss text-grey-500">{{ __('No friends to show yet.') }}</span>
                        </li>
                    @endforelse
                </ul>
            </div>
            @else
            <div class="section full pe-3 ps-4 pt-4 pb-4 position-relative feed-body">
                <h4 class="font-xsssss text-grey-500 text-uppercase fw-700 ls-3">{{ __('Viewing as guest') }}</h4>
                <p class="font-xssss text-grey-600 mb-2">{{ __('You can read the feed. Sign in to post, comment, and connect with others.') }}</p>
                <a href="{{ route('login') }}" class="d-block fw-600 text-primary font-xssss text-decoration-none mb-1">{{ __('Sign in') }}</a>
                <a href="{{ route('register') }}" class="d-block fw-600 text-grey-700 font-xssss text-decoration-none">{{ __('Create an account') }}</a>
            </div>
            @endauth

        </div>
    </div>
    <!-- right chat -->

    <div class="app-footer border-0 shadow-lg bg-primary-gradiant">
        <a href="{{ route('feed.index') }}" class="nav-content-bttn nav-center"><i class="feather-home"></i></a>
        <a href="#" class="nav-content-bttn"><i class="feather-package"></i></a>
        <a href="#" class="nav-content-bttn" data-tab="chats"><i class="feather-layout"></i></a>
        <a href="#" class="nav-content-bttn"><i class="feather-layers"></i></a>
        @auth
        <a href="{{ route('settings.index') }}" class="nav-content-bttn app-footer-settings" title="Settings" aria-label="Settings"><i class="feather-settings"></i></a>
        @else
        <a href="{{ route('login') }}" class="nav-content-bttn app-footer-settings" title="Sign in" aria-label="Sign in"><i class="feather-user"></i></a>
        @endauth
    </div>

    <div class="app-header-search d-none" aria-hidden="true"></div>

</div>

<div
    class="modal fade"
    id="appSearchModal"
    tabindex="-1"
    aria-labelledby="appSearchModalLabel"
    aria-hidden="true"
    data-search-suggest-url="{{ route('search.suggest') }}"
    data-i18n-posts="{{ __('Posts') }}"
    data-i18n-spaces="{{ __('Spaces') }}"
>
    <div class="modal-dialog modal-dialog-centered app-search-modal-dialog">
        <div class="modal-content app-search-modal-content border-0 shadow-none bg-transparent">
            <div class="modal-body app-search-modal-body p-0">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <h5 class="modal-title fw-700 font-xsss text-grey-900 mb-0" id="appSearchModalLabel">{{ __('Search the app') }}</h5>
                    <button type="button" class="btn-close app-search-modal-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form method="GET" action="{{ route('search.index') }}" id="appSearchModalForm">
                    <label for="appSearchModalQuery" class="visually-hidden">{{ __('Search') }}</label>
                    <div class="position-relative app-search-modal-field">
                        <input
                            type="search"
                            name="q"
                            id="appSearchModalQuery"
                            class="form-control rounded-xl bg-greylight border-0 font-xssss fw-500 theme-dark-bg lh-32 py-2 ps-3 app-search-modal-input"
                            placeholder="{{ __('Search...') }}"
                            autocomplete="off"
                            maxlength="255"
                        >
                        <button type="submit" class="app-search-modal-submit" id="appSearchModalSubmit" disabled title="{{ __('Search') }}" aria-label="{{ __('Search') }}">
                            <i class="feather-search" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>
                <div
                    id="appSearchSuggest"
                    class="app-search-suggest mt-2"
                    hidden
                    role="region"
                    aria-live="polite"
                    aria-label="{{ __('Suggestions') }}"
                ></div>
            </div>
        </div>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@livewireScripts
<script>
(function () {
    var modalEl = document.getElementById('appSearchModal');
    if (!modalEl || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        return;
    }
    function getSearchModal() {
        var existing = bootstrap.Modal.getInstance(modalEl);
        if (existing) {
            return existing;
        }
        return new bootstrap.Modal(modalEl);
    }
    var barInput = document.querySelector('.sidebar-header-search-input');
    var modalInput = document.getElementById('appSearchModalQuery');
    var headerForm = document.querySelector('.sidebar-header-search');
    var searchForm = document.getElementById('appSearchModalForm');
    var searchSubmit = document.getElementById('appSearchModalSubmit');
    var suggestEl = document.getElementById('appSearchSuggest');
    var suggestUrl = modalEl.getAttribute('data-search-suggest-url');
    var i18nPosts = modalEl.getAttribute('data-i18n-posts') || 'Posts';
    var i18nSpaces = modalEl.getAttribute('data-i18n-spaces') || 'Spaces';
    var suggestTimer;
    var suggestAbort;
    var suggestReqId = 0;

    function syncSearchSubmitState() {
        if (!modalInput || !searchSubmit) {
            return;
        }
        var ok = modalInput.value.trim().length > 0;
        searchSubmit.disabled = !ok;
        searchSubmit.setAttribute('aria-disabled', ok ? 'false' : 'true');
    }

    function clearSearchSuggest() {
        clearTimeout(suggestTimer);
        suggestReqId += 1;
        if (suggestAbort) {
            suggestAbort.abort();
            suggestAbort = null;
        }
        if (suggestEl) {
            suggestEl.innerHTML = '';
            suggestEl.hidden = true;
        }
    }

    function renderSearchSuggest(data) {
        if (!suggestEl) {
            return;
        }
        var posts = (data && data.posts) ? data.posts : [];
        var spaces = (data && data.spaces) ? data.spaces : [];
        suggestEl.innerHTML = '';
        if (!posts.length && !spaces.length) {
            suggestEl.hidden = true;
            return;
        }
        var inner = document.createElement('div');
        inner.className = 'app-search-suggest-inner app-search-suggest-enter';
        var delay = 0;
        function addSection(labelText, items, textKey, kind) {
            var sec = document.createElement('div');
            sec.className = 'app-search-suggest-section app-search-suggest-section--' + kind;
            var lab = document.createElement('div');
            lab.className = 'app-search-suggest-label';
            lab.textContent = labelText;
            sec.appendChild(lab);
            var ul = document.createElement('ul');
            ul.className = 'app-search-suggest-list list-unstyled mb-0';
            var iconClass = kind === 'posts' ? 'feather-file-text' : 'feather-layers';
            items.forEach(function (item) {
                var li = document.createElement('li');
                li.className = 'app-search-suggest-item';
                li.style.animationDelay = (delay * 45) + 'ms';
                delay += 1;
                var a = document.createElement('a');
                a.className = 'app-search-suggest-link app-search-suggest-link--' + kind;
                a.href = item.url;
                var iconWrap = document.createElement('span');
                iconWrap.className = 'app-search-suggest-icon';
                iconWrap.setAttribute('aria-hidden', 'true');
                var ic = document.createElement('i');
                ic.className = iconClass;
                iconWrap.appendChild(ic);
                var textSpan = document.createElement('span');
                textSpan.className = 'app-search-suggest-text';
                textSpan.textContent = item[textKey] || '';
                a.appendChild(iconWrap);
                a.appendChild(textSpan);
                li.appendChild(a);
                ul.appendChild(li);
            });
            sec.appendChild(ul);
            inner.appendChild(sec);
        }
        if (posts.length) {
            addSection(i18nPosts, posts, 'title', 'posts');
        }
        if (spaces.length) {
            addSection(i18nSpaces, spaces, 'name', 'spaces');
        }
        suggestEl.appendChild(inner);
        suggestEl.hidden = false;
    }

    function fetchSearchSuggest() {
        if (!suggestUrl || !suggestEl || !modalInput) {
            return;
        }
        var q = modalInput.value.trim();
        if (q.length < 2) {
            clearSearchSuggest();
            return;
        }
        if (suggestAbort) {
            suggestAbort.abort();
        }
        suggestAbort = new AbortController();
        var ac = suggestAbort;
        suggestReqId += 1;
        var req = suggestReqId;
        fetch(suggestUrl + '?q=' + encodeURIComponent(q), {
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            signal: ac.signal
        })
            .then(function (r) {
                return r.json();
            })
            .then(function (data) {
                if (req !== suggestReqId) {
                    return;
                }
                renderSearchSuggest(data);
            })
            .catch(function (err) {
                if (err.name === 'AbortError') {
                    return;
                }
            });
    }

    function scheduleSearchSuggest() {
        clearTimeout(suggestTimer);
        suggestTimer = setTimeout(fetchSearchSuggest, 280);
    }

    function openModal(prefill) {
        if (modalInput) {
            modalInput.value = typeof prefill === 'string' ? prefill : '';
            syncSearchSubmitState();
            scheduleSearchSuggest();
        }
        getSearchModal().show();
    }

    if (searchForm && modalInput) {
        searchForm.addEventListener('submit', function (e) {
            if (!modalInput.value.trim()) {
                e.preventDefault();
            }
        });
        modalInput.addEventListener('input', function () {
            syncSearchSubmitState();
            scheduleSearchSuggest();
        });
        modalInput.addEventListener('change', syncSearchSubmitState);
    }

    /* Clicks often land on the feather icon (stacked over the field), not the input */
    if (headerForm) {
        headerForm.addEventListener('click', function (e) {
            e.preventDefault();
            openModal(barInput ? barInput.value.trim() : '');
        });
        headerForm.addEventListener('submit', function (e) {
            e.preventDefault();
            openModal('');
        });
    }
    if (barInput) {
        barInput.addEventListener('focus', function (e) {
            e.preventDefault();
            barInput.blur();
            openModal(barInput.value.trim());
        });
    }

    modalEl.addEventListener('shown.bs.modal', function () {
        if (modalInput) {
            modalInput.focus();
            modalInput.select();
            syncSearchSubmitState();
            scheduleSearchSuggest();
        }
    });

    modalEl.addEventListener('hidden.bs.modal', clearSearchSuggest);

    document.querySelectorAll('[data-app-search-modal]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            openModal('');
        });
    });
})();
</script>
<script>
(function () {
    var btn = document.getElementById('profile-avatar-btn');
    var dropdown = document.getElementById('profile-dropdown');
    var langSub = document.getElementById('profile-lang-submenu');
    var langBtn = document.getElementById('profile-lang-submenu-btn');
    function closeLangSubmenu() {
        if (!langSub || !langBtn) return;
        langSub.style.display = 'none';
        langBtn.setAttribute('aria-expanded', 'false');
    }
    if (!btn || !dropdown) return;
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var next = dropdown.style.display === 'none' || dropdown.style.display === '';
        dropdown.style.display = next ? 'block' : 'none';
        if (dropdown.style.display === 'block') closeLangSubmenu();
    });
    document.addEventListener('click', function () {
        dropdown.style.display = 'none';
        closeLangSubmenu();
    });
    if (!langBtn || !langSub) return;
    langBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        var show = langSub.style.display === 'none' || langSub.style.display === '';
        langSub.style.display = show ? 'block' : 'none';
        langBtn.setAttribute('aria-expanded', show ? 'true' : 'false');
    });
})();

(function () {
    var toggles = document.querySelectorAll('.dark-mode-toggle');
    var icons = document.querySelectorAll('.dark-mode-icon');

    function applyDark(dark) {
        if (dark) {
            document.body.classList.add('theme-dark');
        } else {
            document.body.classList.remove('theme-dark');
        }
        icons.forEach(function(icon) {
            icon.className = (dark ? 'feather-sun' : 'feather-moon') + ' dark-mode-icon font-xl';
            icon.style.opacity = '0.85';
        });
        localStorage.setItem('darkMode', dark ? '1' : '0');
    }

    if (localStorage.getItem('darkMode') === '1') applyDark(true);

    toggles.forEach(function(toggle) {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            var ls = document.getElementById('profile-lang-submenu');
            var lb = document.getElementById('profile-lang-submenu-btn');
            if (ls && lb) {
                ls.style.display = 'none';
                lb.setAttribute('aria-expanded', 'false');
            }
            applyDark(!document.body.classList.contains('theme-dark'));
        });
    });
})();

(function () {
    var nav = document.getElementById('app-left-navigation');
    var main = document.querySelector('.main-wrapper .main-content');
    var btn = document.getElementById('left-sidebar-edge-toggle');
    if (!nav || !main || !btn) return;

    function sync() {
        var collapsed = nav.classList.contains('menu-active');
        document.documentElement.classList.toggle('sidebar-rail-collapsed', collapsed);
        btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        btn.title = collapsed
            ? (btn.getAttribute('data-title-expand') || '')
            : (btn.getAttribute('data-title-collapse') || '');
    }
    btn.setAttribute('data-title-collapse', '{{ __('Collapse sidebar') }}');
    btn.setAttribute('data-title-expand', '{{ __('Expand sidebar') }}');

    btn.addEventListener('click', function () {
        nav.classList.toggle('menu-active');
        main.classList.toggle('menu-active');
        sync();
    });
    sync();
})();

@if(auth()->check())
(function () {
    document.querySelectorAll('#app-left-navigation .sidebar-spaces-heading').forEach(function (head) {
        head.addEventListener('click', function () {
            var panel = head.closest('.sidebar-spaces-panel');
            var cid = head.getAttribute('aria-controls');
            var body = cid ? document.getElementById(cid) : null;
            var expanded = head.getAttribute('aria-expanded') === 'true';
            var next = !expanded;
            head.setAttribute('aria-expanded', next ? 'true' : 'false');
            if (panel) panel.classList.toggle('is-collapsed', !next);
            if (body) body.hidden = !next;
        });
    });
})();
(function () {
    var stack = document.getElementById('sidebar-spaces-stack');
    var raw = document.getElementById('sidebar-spaces-bootstrap');
    var mainList = document.getElementById('sidebar-all-spaces-list');
    if (!stack || !raw || !mainList) return;

    var userId = stack.getAttribute('data-user-id') || '';
    var storageKey = 'ihm.sidebarSpaceFavorites.v1.' + userId;
    var spaces = [];
    try {
        spaces = JSON.parse(raw.textContent || '[]');
    } catch (e) {
        spaces = [];
    }
    var byId = {};
    spaces.forEach(function (s) {
        byId[s.id] = s;
    });

    function readOrder() {
        try {
            var parsed = JSON.parse(localStorage.getItem(storageKey) || '[]');
            return Array.isArray(parsed) ? parsed.filter(function (id, i, a) {
                return typeof id === 'string' && a.indexOf(id) === i;
            }) : [];
        } catch (e) {
            return [];
        }
    }

    function writeOrder(ids) {
        localStorage.setItem(storageKey, JSON.stringify(ids));
    }

    function setStarsForId(id, isFav) {
        stack.querySelectorAll('.sidebar-space-star-btn[data-space-id]').forEach(function (btn) {
            if (btn.getAttribute('data-space-id') !== id) return;
            btn.classList.toggle('is-fav', isFav);
            btn.setAttribute('aria-pressed', isFav ? 'true' : 'false');
        });
    }

    function toggleFavorite(id) {
        var meta = byId[id];
        if (!meta) return;
        var order = readOrder();
        var ix = order.indexOf(id);
        if (ix === -1) {
            order = [id].concat(order.filter(function (x) { return x !== id; }));
        } else {
            order = order.filter(function (x) { return x !== id; });
        }
        writeOrder(order);
        syncAll();
    }

    function syncAll() {
        var favIds = readOrder().filter(function (id) {
            return byId[id];
        });
        var rows = Array.prototype.slice.call(mainList.querySelectorAll('.sidebar-space-row[data-space-id]'));

        rows.sort(function (a, b) {
            var ida = a.getAttribute('data-space-id');
            var idb = b.getAttribute('data-space-id');
            var ixa = favIds.indexOf(ida);
            var ixb = favIds.indexOf(idb);
            var af = ixa !== -1;
            var bf = ixb !== -1;
            if (af && bf) return ixa - ixb;
            if (af) return -1;
            if (bf) return 1;
            var na = (byId[ida] && byId[ida].name) || '';
            var nb = (byId[idb] && byId[idb].name) || '';
            return na.localeCompare(nb, undefined, { sensitivity: 'base' });
        });

        rows.forEach(function (row) {
            mainList.appendChild(row);
        });

        spaces.forEach(function (s) {
            setStarsForId(s.id, favIds.indexOf(s.id) !== -1);
        });
    }

    stack.querySelectorAll('.sidebar-space-star-btn[data-space-id]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var id = btn.getAttribute('data-space-id');
            if (id) toggleFavorite(id);
        });
    });

    syncAll();
})();
@endif
</script>
@yield('scripts')
</body>
</html>
