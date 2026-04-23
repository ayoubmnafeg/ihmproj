@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<h2 class="fw-700 display1-size display2-md-size mb-3">Login into <br>your account</h2>

@if ($errors->any())
    <div class="alert alert-danger font-xsss mb-3">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group icon-input mb-3">
        <i class="font-sm ti-user text-grey-500 pe-0"></i>
        <input type="text" name="username" value="{{ old('username') }}"
               class="style2-input ps-5 form-control text-grey-900 font-xsss fw-600"
               placeholder="Your Username" required>
    </div>

    <div class="form-group icon-input mb-1">
        <input type="password" name="password"
               class="style2-input ps-5 form-control text-grey-900 font-xss ls-3"
               placeholder="Password" required>
        <i class="font-sm ti-lock text-grey-500 pe-0"></i>
    </div>

    <div class="form-check text-left mb-3">
        <input type="checkbox" name="remember" class="form-check-input mt-2" id="remember">
        <label class="form-check-label font-xsss text-grey-500" for="remember">Remember me</label>
        <a href="{{ route('forgot-password') }}" class="fw-600 font-xsss text-grey-700 mt-1 float-right">Forgot your Password?</a>
    </div>

    <div class="col-sm-12 p-0 text-left">
        <div class="form-group mb-1">
            <button type="submit" class="form-control text-center style2-input text-white fw-600 bg-dark border-0 p-0">
                Login
            </button>
        </div>
        <h6 class="text-grey-500 font-xsss fw-500 mt-0 mb-0 lh-32">
            Don't have account <a href="{{ route('register') }}" class="fw-700 ms-1">Register</a>
        </h6>
    </div>

</form>
@endsection
