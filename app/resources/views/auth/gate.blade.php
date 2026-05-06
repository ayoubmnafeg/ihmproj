@extends('layouts.auth')

@section('body_class', 'auth-gate-page ')

@section('title', $initialPanel === 'register' ? __('Register') : __('Login'))

@section('content')
@php($isRegister = $initialPanel === 'register')
<div id="auth-gate-root"
     class="auth-gate-root"
     data-active="{{ $isRegister ? 'register' : 'login' }}"
     data-url-login="{{ url('/login') }}"
     data-url-register="{{ url('/register') }}"
     data-title-login="{{ __('Login') }}"
     data-title-register="{{ __('Register') }}"
     data-app-name="{{ config('app.name') }}">

    @if ($errors->any())
        <div class="alert alert-danger font-xsss mb-3 auth-gate-errors" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="auth-forms-viewport">
        <div class="auth-forms-track" id="auth-forms-track" style="transform: translateX({{ $isRegister ? '-50%' : '0%' }});">
            <div class="auth-form-slide" id="auth-slide-login" role="tabpanel" aria-labelledby="auth-tab-login" @if ($isRegister) aria-hidden="true" @else aria-hidden="false" @endif>
                <div class="card shadow-none border-0 auth-form-slide-card">
                    <div class="card-body rounded-0 text-left px-1 px-sm-2">
                        <h2 class="fw-700 display1-size display2-md-size mb-3 header-title">{!! __('Login into <br>your account') !!}</h2>
                        @include('auth.partials.login-form')
                    </div>
                </div>
            </div>
            <div class="auth-form-slide" id="auth-slide-register" role="tabpanel" aria-labelledby="auth-tab-register" @if ($isRegister) aria-hidden="false" @else aria-hidden="true" @endif>
                <div class="card shadow-none border-0 auth-form-slide-card">
                    <div class="card-body rounded-0 text-left px-1 px-sm-2">
                        <h2 class="fw-700 display1-size display2-md-size mb-4 header-title">{!! __('Create <br>your account') !!}</h2>
                        @include('auth.partials.register-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var adjectives = ['Silent','Brave','Swift','Clever','Bold','Calm','Dark','Epic','Fierce','Gentle','Happy','Iron','Jolly','Kind','Lucky','Mighty','Noble','Odd','Proud','Quick','Rapid','Sharp','Tough','Ultra','Vivid','Wild','Zany','Amber','Blaze','Coral','Dusk','Ember','Frost','Gloom','Haze','Ivory','Jade','Keen','Lime','Mist','Neon','Opal','Ruby','Sage','Teal','Angry','Azure','Bitter','Bright','Bronze','Cloudy','Crazy','Crimson','Cyber','Dizzy','Dusty','Eternal','Faded','Famous','Fancy','Frozen','Funky','Fuzzy','Giant','Gloomy','Golden','Goofy','Grand','Grave','Grim','Grumpy','Hidden','Hollow','Hyper','Icy','Jumpy','Laser','Lazy','Lone','Lush','Magic','Massive','Mega','Messy','Metal','Mystic','Nifty','Nutty','Obsessed','Phantom','Plucky','Polar','Prickly','Psycho','Rogue','Royal','Rusty','Savage','Shadowy','Shiny','Sneaky','Solar','Spicy','Stormy','Striped','Super','Turbo','Twisted','Wicked','Windy','Woolly'];
var nouns = ['Fox','Panda','Tiger','Wolf','Eagle','Bear','Lion','Shark','Hawk','Deer','Lynx','Crow','Mole','Newt','Owl','Pike','Quail','Raven','Seal','Toad','Viper','Wasp','Yak','Bison','Cobra','Drake','Finch','Goat','Hyena','Ibis','Jackal','Kite','Llama','Moose','Narwhal','Otter','Parrot','Rhino','Sloth','Tapir','Urial','Vole','Walrus','Xerus','Zebra','Ant','Bat','Bug','Cat','Dog','Elk','Emu','Gnu','Hog','Jay','Ram','Rat','Roe','Slug','Snail','Swan','Wren','Crab','Clam','Frog','Gull','Lark','Mink','Moth','Mule','Pony','Pup','Stag','Trout','Tuna','Worm','Colt','Dove','Duck','Fawn','Fish','Gnat','Hare','Koi','Lamb','Loon','Mare','Mite','Oxen','Pika','Puma','Shrew','Skunk','Snipe','Stoat','Swift','Tern','Vixen'];

function generateUsername() {
    var adj = adjectives[Math.floor(Math.random() * adjectives.length)];
    var noun = nouns[Math.floor(Math.random() * nouns.length)];
    var num = Math.floor(Math.random() * 900) + 100;
    document.getElementById('username-input').value = adj + noun + num;
}

(function () {
    var root = document.getElementById('auth-gate-root');
    var track = document.getElementById('auth-forms-track');
    var slideLogin = document.getElementById('auth-slide-login');
    var slideRegister = document.getElementById('auth-slide-register');
    var nav = document.querySelector('.auth-nav-segment');
    if (!root || !track || !nav) return;

    var urlLogin = root.dataset.urlLogin;
    var urlRegister = root.dataset.urlRegister;
    var titleLogin = root.dataset.titleLogin;
    var titleRegister = root.dataset.titleRegister;
    var appName = root.dataset.appName;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function setPanel(panel, opts) {
        opts = opts || {};
        var animate = opts.animate !== false && !reduceMotion;
        if (!animate) track.style.transition = 'none';
        else track.style.transition = '';

        var isReg = panel === 'register';
        track.style.transform = isReg ? 'translateX(-50%)' : 'translateX(0)';
        slideLogin.setAttribute('aria-hidden', isReg ? 'true' : 'false');
        slideRegister.setAttribute('aria-hidden', isReg ? 'false' : 'true');
        root.dataset.active = isReg ? 'register' : 'login';

        nav.querySelectorAll('a').forEach(function (a) {
            var p = a.getAttribute('data-auth-panel');
            var on = (p === 'register') === isReg;
            a.classList.toggle('active', on);
            if (on) a.setAttribute('aria-current', 'page');
            else a.removeAttribute('aria-current');
        });

        var path = isReg ? urlRegister : urlLogin;
        if (opts.replaceUrl && window.history && window.history.replaceState) {
            window.history.replaceState({ authPanel: panel }, '', path);
        }
        document.title = appName + ' - ' + (isReg ? titleRegister : titleLogin);

        if (!animate) {
            requestAnimationFrame(function () {
                track.style.transition = '';
            });
        }
    }

    nav.addEventListener('click', function (e) {
        var a = e.target.closest('a[data-auth-panel]');
        if (!a) return;
        if (!document.body.classList.contains('auth-gate-page')) return;
        var panel = a.getAttribute('data-auth-panel');
        if (panel !== 'login' && panel !== 'register') return;
        var current = root.dataset.active === 'register' ? 'register' : 'login';
        if (panel === current) {
            e.preventDefault();
            return;
        }
        e.preventDefault();
        setPanel(panel, { animate: true, replaceUrl: true });
    });

    var ui = document.getElementById('username-input');
    if (ui && !ui.value) generateUsername();
})();
</script>
@endsection
