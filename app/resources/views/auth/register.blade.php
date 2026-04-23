@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<h2 class="fw-700 display1-size display2-md-size mb-4">Create <br>your account</h2>

@if ($errors->any())
    <div class="alert alert-danger font-xsss mb-3">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group mb-3">
        <label class="font-xsss text-grey-500 fw-600 mb-1">Your username</label>
        <div class="d-flex gap-2 align-items-center">
            <input type="text" name="display_name" id="username-input"
                   value="{{ old('display_name') }}"
                   class="style2-input form-control text-grey-900 font-xsss fw-600"
                   placeholder="Username" maxlength="30" required>
            <button type="button" onclick="generateUsername()"
                    title="Generate new username"
                    class="btn-round-md bg-greylight border-0 d-flex align-items-center justify-content-center"
                    style="min-width:40px;height:40px;cursor:pointer;flex-shrink:0;">
                <i class="feather-refresh-cw text-grey-700 font-sm"></i>
            </button>
        </div>
    </div>

    <div class="form-group icon-input mb-3">
        <i class="font-sm ti-email text-grey-500 pe-0"></i>
        <input type="email" name="email" value="{{ old('email') }}"
               class="style2-input ps-5 form-control text-grey-900 font-xsss fw-600"
               placeholder="Your Email Address" required>
    </div>

    <div class="form-group mb-3">
        <input type="hidden" name="gender" id="gender-input" value="{{ old('gender', '') }}">
        <div class="d-flex gap-2">
            <button type="button" id="btn-male" onclick="selectGender('male')"
                    class="gender-btn flex-fill style2-input form-control fw-600 font-xsss border d-flex align-items-center justify-content-center gap-2"
                    style="cursor:pointer;">
                <img src="{{ asset('images/male.svg') }}" width="18" height="18" class="gender-icon"> Male
            </button>
            <button type="button" id="btn-female" onclick="selectGender('female')"
                    class="gender-btn flex-fill style2-input form-control fw-600 font-xsss border d-flex align-items-center justify-content-center gap-2"
                    style="cursor:pointer;">
                <img src="{{ asset('images/female.svg') }}" width="18" height="18" class="gender-icon"> Female
            </button>
        </div>
    </div>

    <div class="form-group icon-input mb-3">
        <input type="password" name="password"
               class="style2-input ps-5 form-control text-grey-900 font-xss ls-3"
               placeholder="Password" required>
        <i class="font-sm ti-lock text-grey-500 pe-0"></i>
    </div>

    <div class="form-group icon-input mb-3">
        <input type="password" name="password_confirmation"
               class="style2-input ps-5 form-control text-grey-900 font-xss ls-3"
               placeholder="Confirm Password" required>
        <i class="font-sm ti-lock text-grey-500 pe-0"></i>
    </div>

    <div class="col-sm-12 p-0 text-left mt-2">
        <div class="form-group mb-1">
            <button type="submit" class="form-control text-center style2-input text-white fw-600 bg-dark border-0 p-0">
                Register
            </button>
        </div>
        <h6 class="text-grey-500 font-xsss fw-500 mt-0 mb-0 lh-32">
            Already have account <a href="{{ route('login') }}" class="fw-700 ms-1">Login</a>
        </h6>
    </div>
</form>
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

if (!document.getElementById('username-input').value) generateUsername();

function selectGender(val) {
    document.getElementById('gender-input').value = val;
    document.querySelectorAll('.gender-btn').forEach(function(btn) {
        btn.style.background = '';
        btn.style.color = '';
        btn.style.borderColor = '';
    });
    var colors = { male: '#1d4ed8', female: '#ec4899' };
    var active = document.getElementById('btn-' + val);
    active.style.background = colors[val];
    active.style.color = '#fff';
    active.style.borderColor = colors[val];
}

var initial = document.getElementById('gender-input').value;
if (initial) selectGender(initial);
</script>
@endsection
