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
    </style>
</head>
<body class="ui-private color-theme-blue">

<div class="preloader"></div>

<div class="main-wrap">
    <div class="nav-header bg-transparent shadow-none border-0 position-absolute w-100" style="top: 0; z-index: 10;">
        <div class="nav-top w-100">
            <a href="{{ route('home') }}" class="text-decoration-none d-inline-flex align-items-center gap-2 app-brand-link">
                <i class="feather-shield text-grey-600" style="font-size:1.5rem;" aria-hidden="true"></i>
                <span class="d-inline-block fw-600 text-grey-900 logo-text mb-0" style="font-family:Inter,system-ui,sans-serif;letter-spacing:0.04em;">{{ config('app.name') }}</span>
            </a>
            <a href="#" class="mob-menu ms-auto me-2 chat-active-btn"><i class="feather-message-circle text-grey-900 font-sm btn-round-md bg-greylight"></i></a>
            <a href="#" class="me-2 menu-search-icon mob-menu"><i class="feather-search text-grey-900 font-sm btn-round-md bg-greylight"></i></a>
            <button class="nav-menu me-0 ms-2"></button>
            <div class="dropdown ms-3" style="position: relative;">
                <button type="button" class="header-btn d-inline-flex align-items-center bg-white text-dark font-xsss p-2 ps-3 pe-3 text-center lh-20 rounded-xl fw-600 border shadow-sm" id="langDropdown" style="outline:none;">
                    <i class="feather-globe me-1"></i> {{ strtoupper(app()->getLocale() == 'fr' ? 'fr' : 'en') }} <i class="feather-chevron-down ms-1" style="font-size:10px;"></i>
                </button>
                <div class="dropdown-menu shadow-sm border-0 p-2 rounded-3" id="langMenuChoices" style="display: none; position: absolute; right: 0; top: 100%; z-index: 100000; min-width: 120px;">
                    <a class="dropdown-item fw-600 font-xsss rounded-3 mb-1" href="{{ route('lang.switch', 'en') }}">English</a>
                    <a class="dropdown-item fw-600 font-xsss rounded-3" href="{{ route('lang.switch', 'fr') }}">Français</a>
                </div>
            </div>
            <button id="dark-mode-toggle" class="p-2 text-center ms-2 menu-icon border-0 bg-transparent cursor-pointer header-tool" style="outline:none;" type="button">
                <i id="dark-mode-icon" class="feather-moon font-xl" aria-hidden="true"></i>
            </button>
            <a href="{{ route('login') }}" class="header-btn d-none d-lg-block bg-dark fw-500 text-white font-xsss p-3 px-4 ms-3 text-center lh-20 rounded-xl">{{ __('Login') }}</a>
            <a href="{{ route('register') }}" class="header-btn d-none d-lg-block bg-current fw-500 text-white font-xsss p-3 px-4 ms-2 text-center lh-20 rounded-xl">{{ __('Register') }}</a>
        </div>
    </div>

    <div class="row w-100 m-0">
        <div class="col-xl-5 d-none d-xl-block p-0 min-vh-100 bg-no-repeat"
             style="background-image: url({{ asset('images/' . ($bgImage ?? 'Image_Login.webp')) }}); background-position: center; background-size: cover; box-shadow: inset 0 0 150px rgba(0,0,0,0.1);"></div>
        <div class="col-xl-7 min-vh-100 align-items-center d-flex bg-white" style="padding-top: 80px; padding-bottom: 20px; overflow-y: auto;">
            <div class="card shadow-none border-0 ms-auto me-auto w-100" style="max-width: 480px;">
                <div class="card-body rounded-0 text-left" style="animation: fadeIn 0.8s ease-out;">
                    @yield('content')
                </div>
            </div>
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
                icon.className = dark ? 'feather-sun font-xl' : 'feather-moon font-xl';
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
