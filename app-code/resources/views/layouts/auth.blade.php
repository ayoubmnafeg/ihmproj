<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/theme-private.css') }}">
    <style>
        /* Premium Layout Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .style2-input {
            border-radius: 12px !important;
            border: 1px solid #e1e5eb;
            transition: all 0.3s ease;
            background-color: #f8f9fc !important;
            box-shadow: none !important;
            height: 55px;
        }
        .style2-input:focus {
            border-color: #3b82f6 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
        }
        .form-group.icon-input i {
            color: #94a3b8 !important;
            transition: color 0.3s ease;
            margin-top: 5px; /* Center align fix */
        }
        .form-group.icon-input:focus-within i {
            color: #3b82f6 !important;
        }
        .btn-submit {
            border-radius: 12px !important;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 55px !important;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px rgba(15, 23, 42, 0.5) !important;
        }
        .header-title {
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            letter-spacing: -1px;
        }
        /* Auth pages: compact header with segment switcher (override app header flex) */
        body.ui-private .nav-header .nav-top.auth-nav-row {
            justify-content: space-between !important;
            align-items: center;
        }
        .auth-nav-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem 0.75rem;
            padding: 0.4rem 1rem;
            max-width: 100%;
            min-height: 0;
        }
        .auth-nav-brand-icon {
            font-size: 1.2rem !important;
        }
        .auth-nav-brand .logo-text {
            font-size: 0.9rem !important;
        }
        @media (min-width: 1200px) {
            .auth-nav-row { padding: 0.45rem 1.5rem; }
        }
        .auth-nav-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.375rem;
            margin-left: auto;
        }
        /*
         * style.css repeats .nav-header .nav-top a { line-height: 90px } after a 28px fix,
         * beating our weaker .auth-nav-segment selectors — Login/Register were stretching to bar height.
         */
        body.ui-private .nav-header .nav-top.auth-nav-row .auth-nav-segment {
            display: inline-flex !important;
            align-items: center !important;
            align-self: center !important;
            flex: 0 0 auto !important;
            width: auto !important;
            height: auto !important;
            line-height: normal !important;
            background: linear-gradient(180deg, #eef1f6 0%, #e8ecf2 100%);
            border-radius: 10px;
            padding: 3px;
            gap: 3px;
            border: 1px solid rgba(148, 163, 184, 0.38);
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.7);
        }
        body.ui-private .nav-header .nav-top.auth-nav-row .auth-nav-segment a {
            font-size: 0.8125rem !important;
            line-height: 1.35 !important;
            width: auto !important;
            min-height: 0 !important;
            height: auto !important;
            margin-bottom: 0 !important;
            padding: 0.4rem 0.8rem !important;
            text-align: center !important;
            display: inline-flex !important;
            align-items: center !important;
            align-self: center !important;
            flex: 0 0 auto !important;
            white-space: nowrap !important;
        }
        .auth-nav-segment a {
            padding: 0.4rem 0.8rem;
            min-height: 0 !important;
            height: auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            white-space: nowrap;
            line-height: 1.35;
            background: transparent;
            transition: background 0.18s ease, color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }
        .auth-nav-segment a:hover:not(.active) {
            color: #0f172a;
            background: rgba(255, 255, 255, 0.55);
        }
        .auth-nav-segment a:focus-visible {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        .auth-nav-segment a.active {
            font-weight: 600;
            background: #fff;
            color: #0f172a;
            box-shadow:
                0 1px 2px rgba(15, 23, 42, 0.05),
                0 0 0 1px rgba(148, 163, 184, 0.28);
        }
        .auth-nav-divider {
            width: 1px;
            height: 1.25rem;
            background: #e2e8f0;
            flex-shrink: 0;
            align-self: center;
        }
        .auth-nav-utilities {
            display: flex;
            align-items: center;
            gap: 0.125rem;
        }
        /* Lang + theme — scale with Login/Register */
        .auth-lang-btn.header-btn {
            padding: 0.4rem 0.65rem !important;
            font-size: 0.8125rem !important;
            line-height: 1.35 !important;
            border-radius: 0.55rem !important;
        }
        .auth-lang-btn .feather-globe {
            font-size: 15px !important;
        }
        .auth-lang-btn .feather-chevron-down {
            margin-left: 0.125rem !important;
        }
        .auth-theme-btn {
            padding: 0.3rem !important;
            border-radius: 0.5rem !important;
        }
        .auth-theme-btn .feather {
            font-size: 1.25rem !important;
        }
        /* Same structural specificity as light block above (template line-height trumped light rules) */
        body.theme-dark.ui-private .nav-header .nav-top.auth-nav-row .auth-nav-segment {
            background: linear-gradient(180deg, #1a2332 0%, #161f2c 100%) !important;
            border-color: rgba(148, 163, 184, 0.25) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04) !important;
        }
        body.theme-dark.ui-private .nav-header .nav-top.auth-nav-row .auth-nav-segment a {
            color: #94a3b8 !important;
            font-weight: 500;
        }
        body.theme-dark.ui-private .nav-header .nav-top.auth-nav-row .auth-nav-segment a:hover:not(.active) {
            color: #e2e8f0 !important;
            background: rgba(255, 255, 255, 0.06) !important;
        }
        body.theme-dark.ui-private .nav-header .nav-top.auth-nav-row .auth-nav-segment a.active {
            font-weight: 600;
            background: #334155 !important;
            color: #f8fafc !important;
            box-shadow:
                0 1px 3px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(148, 163, 184, 0.2);
        }
        body.theme-dark .auth-nav-divider {
            background: #334155;
        }
        /* Unified login/register: full right-rail sliding panels */
        .auth-right-rail {
            overflow-x: hidden !important;
        }
        body.auth-gate-page .auth-gate-root {
            flex: 1 1 0%;
            min-height: 0;
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 100%;
        }
        .auth-gate-errors {
            flex-shrink: 0;
            width: 100%;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        .auth-forms-viewport {
            flex: 1 1 0%;
            min-height: 0;
            width: 100%;
            overflow: hidden;
        }
        .auth-forms-track {
            display: flex;
            flex-direction: row;
            width: 200%;
            min-height: 100%;
            height: 100%;
            transition: transform 0.48s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
            align-items: stretch;
        }
        .auth-form-slide {
            flex: 0 0 50%;
            width: 50%;
            max-width: 50%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100%;
            padding-left: clamp(0.75rem, 4vw, 1.75rem);
            padding-right: clamp(0.75rem, 4vw, 1.75rem);
            padding-bottom: max(1.25rem, env(safe-area-inset-bottom, 0px));
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        .auth-form-slide .auth-form-slide-card {
            width: 100%;
            max-width: 480px;
            animation: fadeIn 0.5s ease-out;
        }
        body.theme-dark.ui-private .auth-right-rail {
            background: var(--ui-bg) !important;
        }
        body.theme-dark .auth-form-slide .header-title {
            color: #f8fafc;
        }
        @media (prefers-reduced-motion: reduce) {
            .auth-forms-track {
                transition: none !important;
            }
            .auth-form-slide .auth-form-slide-card {
                animation: none;
            }
        }
        body.auth-gate-page .auth-nav-segment a[data-auth-panel] {
            cursor: pointer;
        }
        /* Hero panel — full-bleed photo + anonyme marketing column */
        .auth-hero-visual {
            --auth-hero-accent: #c4b5fd;
            --auth-hero-accent-dim: #a78bfa;
            position: relative;
            isolation: isolate;
            min-height: 100vh;
            align-self: stretch;
            overflow: hidden;
            background:
                radial-gradient(ellipse 90% 70% at 20% 30%, rgba(88, 28, 135, 0.35) 0%, transparent 55%),
                linear-gradient(165deg, #0a0612 0%, #030712 50%, #020617 100%);
        }
        /*
         * Image display height = hero column height exactly (top..bottom of .auth-hero-illustration).
         * Width is automatic from aspect ratio; centered; sides clip if wider than column (no height change).
         */
        .auth-hero-illustration {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }
        .auth-hero-bg-img {
            position: absolute;
            left: 50%;
            top: 0;
            height: 100%;
            width: auto;
            transform: translateX(-50%);
            opacity: 0.92;
        }
        .auth-hero-trust {
            position: absolute;
            right: clamp(1.1rem, 3.5vw, 2rem);
            bottom: clamp(1.1rem, 3vw, 1.85rem);
            z-index: 3;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            margin: 0;
            max-width: calc(100% - 2.5rem);
            font-family: Inter, system-ui, sans-serif;
            font-size: 0.6875rem;
            font-weight: 500;
            letter-spacing: 0.055em;
            line-height: 1.25;
            color: rgba(203, 213, 225, 0.7);
            text-shadow: 0 1px 14px rgba(0, 0, 0, 0.5);
            pointer-events: none;
        }
        .auth-hero-trust .feather {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
            opacity: 0.88;
            stroke: currentColor;
        }
        .auth-hero-visual::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            background:
                linear-gradient(90deg, rgba(2, 6, 23, 0.88) 0%, rgba(2, 6, 23, 0.55) 42%, rgba(2, 6, 23, 0.25) 72%, rgba(2, 6, 23, 0.5) 100%),
                radial-gradient(ellipse 80% 70% at 50% 100%, rgba(0, 0, 0, 0.75) 0%, transparent 55%);
            box-shadow:
                inset 0 0 120px rgba(0, 0, 0, 0.45),
                inset 0 -60px 140px rgba(2, 6, 23, 0.9);
        }
        .row.auth-layout-row {
            min-height: 100vh;
            align-items: stretch;
        }
        .auth-hero-panel {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 100vh;
            padding: clamp(5.5rem, 10vw, 7rem) clamp(2rem, 5vw, 4rem) clamp(2.5rem, 6vw, 3rem);
            font-family: Inter, system-ui, sans-serif;
            pointer-events: none;
            text-align: left;
            max-width: 44rem;
        }
        .auth-hero-title {
            margin: 0 0 clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            letter-spacing: -0.045em;
            line-height: 0.98;
            text-shadow: 0 4px 36px rgba(0, 0, 0, 0.5);
        }
        .auth-hero-title-line1 {
            display: block;
            font-size: clamp(3rem, 7.5vw, 4.75rem);
            color: #f8fafc;
        }
        .auth-hero-title-line2-wrap {
            display: block;
            position: relative;
            margin-top: 0.12em;
            font-size: clamp(3rem, 7.5vw, 4.75rem);
            color: var(--auth-hero-accent);
        }
        .auth-hero-title-underline-letter {
            position: relative;
            display: inline-block;
        }
        .auth-hero-title-underline-letter::after {
            content: '';
            position: absolute;
            left: 0.05em;
            right: -0.02em;
            bottom: 0.08em;
            height: 0.07em;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--auth-hero-accent-dim), var(--auth-hero-accent));
            opacity: 0.95;
        }
        .auth-hero-sub {
            margin: 0 0 clamp(2.25rem, 5vw, 3.25rem);
            max-width: 28rem;
            font-size: clamp(1rem, 1.35vw, 1.0625rem);
            font-weight: 500;
            line-height: 1.55;
            color: #e2e8f0;
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.4);
        }
        .auth-hero-sub .auth-hero-accent {
            color: var(--auth-hero-accent);
            font-weight: 600;
        }
        .auth-hero-features {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: clamp(1.25rem, 3.5vw, 2.25rem);
            align-items: start;
            max-width: 100%;
        }
        .auth-hero-feature {
            text-align: center;
        }
        .auth-hero-feature-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.85rem;
            width: 3.375rem;
            height: 3.375rem;
            color: var(--auth-hero-accent);
            filter: drop-shadow(0 2px 10px rgba(0, 0, 0, 0.35));
        }
        .auth-hero-feature-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        .auth-hero-feature p {
            margin: 0;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.35;
            color: rgba(248, 250, 252, 0.92);
            text-shadow: 0 1px 14px rgba(0, 0, 0, 0.35);
        }
        @media (max-width: 400px) {
            .auth-hero-features {
                gap: 0.75rem;
            }
            .auth-hero-feature p {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body class="ui-private color-theme-blue @yield('body_class')">

<div class="preloader"></div>

<div class="main-wrap">
    <div class="nav-header bg-transparent shadow-none border-0 position-absolute w-100" style="top: 0; z-index: 10; border-bottom: 1px solid rgba(148, 163, 184, 0.25) !important;">
        <div class="nav-top w-100 auth-nav-row">
            <a href="{{ route('home') }}" class="text-decoration-none d-inline-flex align-items-center gap-2 app-brand-link auth-nav-brand">
                <i class="feather-shield text-grey-600 auth-nav-brand-icon" aria-hidden="true"></i>
                <span class="d-inline-block fw-600 text-grey-900 logo-text mb-0" style="font-family:Inter,system-ui,sans-serif;letter-spacing:0.04em;">{{ config('app.name') }}</span>
            </a>
            <div class="auth-nav-actions">
                @php
                    $authSegmentLogin = request()->routeIs('login');
                    $authSegmentRegister = request()->routeIs('register');
                @endphp
                <nav class="auth-nav-segment" role="tablist" aria-label="{{ __('Account') }}">
                    <a href="{{ route('login') }}" role="tab" data-auth-panel="login" id="auth-tab-login" class="{{ $authSegmentLogin ? 'active' : '' }}" @if ($authSegmentLogin) aria-current="page" @endif>{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" role="tab" data-auth-panel="register" id="auth-tab-register" class="{{ $authSegmentRegister ? 'active' : '' }}" @if ($authSegmentRegister) aria-current="page" @endif>{{ __('Register') }}</a>
                </nav>
                <span class="auth-nav-divider d-none d-sm-block" aria-hidden="true"></span>
                <div class="auth-nav-utilities">
                    <div class="dropdown" style="position: relative;">
                        <button type="button" class="auth-lang-btn header-btn d-inline-flex align-items-center bg-white text-dark font-xsss text-center lh-20 fw-600 border shadow-sm" id="langDropdown" style="outline:none;" aria-expanded="false" aria-haspopup="true" aria-controls="langMenuChoices">
                            <i class="feather-globe me-1 text-grey-500" aria-hidden="true"></i> {{ strtoupper(app()->getLocale() == 'fr' ? 'fr' : 'en') }} <i class="feather-chevron-down ms-1 text-grey-500" style="font-size:10px;" aria-hidden="true"></i>
                        </button>
                        <div class="dropdown-menu shadow-lg border-0 p-2 rounded-3" id="langMenuChoices" role="menu" style="display: none; position: absolute; right: 0; top: 100%; z-index: 100000; min-width: 140px;">
                            <a class="dropdown-item fw-600 font-xsss rounded-3 mb-1 d-flex align-items-center gap-2" role="menuitem" href="{{ route('lang.switch', 'en') }}">
                                <img src="{{ asset('images/lang-en.png') }}" alt="" width="20" height="20" class="rounded-circle"> English
                            </a>
                            <a class="dropdown-item fw-600 font-xsss rounded-3 d-flex align-items-center gap-2" role="menuitem" href="{{ route('lang.switch', 'fr') }}">
                                <img src="{{ asset('images/lang-fr.png') }}" alt="" width="20" height="20" class="rounded-circle"> Français
                            </a>
                        </div>
                    </div>
                    <button id="dark-mode-toggle" class="auth-theme-btn text-center border-0 bg-transparent cursor-pointer header-tool" style="outline:none;" type="button" aria-label="{{ __('Toggle dark mode') }}">
                        <i id="dark-mode-icon" class="feather feather-moon" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row auth-layout-row w-100 m-0">
        <div class="col-xl-5 d-none d-xl-block p-0 auth-hero-visual">
            <div class="auth-hero-illustration" aria-hidden="true">
                <img src="{{ asset('images/' . ($bgImage ?? 'auth/auth-hero-bg.png')) }}"
                     alt=""
                     class="auth-hero-bg-img"
                     decoding="async"
                     fetchpriority="low">
            </div>
            <div class="auth-hero-panel">
                <h1 class="auth-hero-title">
                    <span class="auth-hero-title-line1">{{ __('Be') }}</span>
                    <span class="auth-hero-title-line2-wrap">{!! __('Hero title anonyme') !!}</span>
                </h1>
                <p class="auth-hero-sub">{!! __('Hero welcome line') !!}</p>
                <div class="auth-hero-features">
                    <div class="auth-hero-feature">
                        <div class="auth-hero-feature-icon" aria-hidden="true">
                            <img src="{{ asset('images/auth/hero-icon-mask.png') }}" alt="" width="54" height="54" decoding="async">
                        </div>
                        <p>{{ __('No identity') }}</p>
                    </div>
                    <div class="auth-hero-feature">
                        <div class="auth-hero-feature-icon" aria-hidden="true">
                            <img src="{{ asset('images/auth/hero-icon-shield.png') }}" alt="" width="54" height="54" decoding="async">
                        </div>
                        <p>{{ __('Your privacy is protected') }}</p>
                    </div>
                    <div class="auth-hero-feature">
                        <div class="auth-hero-feature-icon" aria-hidden="true">
                            <img src="{{ asset('images/auth/hero-icon-user.png') }}" alt="" width="54" height="54" decoding="async">
                        </div>
                        <p>{{ __('Be yourself, anonymously') }}</p>
                    </div>
                </div>
            </div>
            <p class="auth-hero-trust">
                <i class="feather feather-lock" aria-hidden="true"></i>
                <span>{{ __('Private. Secure. Anonymous.') }}</span>
            </p>
        </div>
        <div class="auth-right-rail col-xl-7 min-vh-100 d-flex flex-column bg-white" style="padding-top: 80px;">
            @yield('content')
        </div>
    </div>
</div>

<script src="{{ asset('js/plugin.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/scripts.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var langBtn = document.getElementById('langDropdown');
        var langMenu = document.getElementById('langMenuChoices');
        if (langBtn && langMenu) {
            langBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                langMenu.style.display = langMenu.style.display === 'block' ? 'none' : 'block';
            });
            document.addEventListener('click', function() {
                langMenu.style.display = 'none';
            });
        }
    });

    (function () {
        var toggle = document.getElementById('dark-mode-toggle');
        var icon = document.getElementById('dark-mode-icon');

        function applyDark(dark) {
            if (dark) {
                document.body.classList.add('theme-dark');
            } else {
                document.body.classList.remove('theme-dark');
            }
            if(icon) {
                icon.className = dark ? 'feather feather-sun' : 'feather feather-moon';
            }
            localStorage.setItem('darkMode', dark ? '1' : '0');
        }

        if (localStorage.getItem('darkMode') === '1') applyDark(true);

        if(toggle) {
            toggle.addEventListener('click', function () {
                applyDark(!document.body.classList.contains('theme-dark'));
            });
        }
    })();
</script>
@yield('scripts')
</body>
</html>
