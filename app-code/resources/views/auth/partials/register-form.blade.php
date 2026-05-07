<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group mb-3">
        <label class="font-xsss text-grey-500 fw-600 mb-1">{{ __('Your username') }}</label>
        <div class="d-flex gap-2 align-items-center">
            <input type="text" name="display_name" id="username-input"
                   value="{{ old('display_name') }}"
                   class="style2-input form-control text-grey-900 font-xsss fw-600"
                   placeholder="{{ __('Username') }}" maxlength="30" required autocomplete="username">
            <button type="button" onclick="generateUsername()"
                    title="{{ __('Generate new username') }}"
                    class="btn-round-md bg-greylight border-0 d-flex align-items-center justify-content-center"
                    style="min-width:40px;height:40px;cursor:pointer;flex-shrink:0;">
                <i class="feather-refresh-cw text-grey-700 font-sm"></i>
            </button>
        </div>
    </div>

    <div class="form-group icon-input mb-3">
        <input type="password" name="password"
               class="style2-input ps-5 form-control text-grey-900 font-xss ls-3"
               placeholder="{{ __('Password') }}" required autocomplete="new-password">
        <i class="font-sm ti-lock text-grey-500 pe-0"></i>
    </div>

    <div class="form-group icon-input mb-3">
        <input type="password" name="password_confirmation"
               class="style2-input ps-5 form-control text-grey-900 font-xss ls-3"
               placeholder="{{ __('Confirm Password') }}" required autocomplete="new-password">
        <i class="font-sm ti-lock text-grey-500 pe-0"></i>
    </div>

    <div class="col-sm-12 p-0 text-left mt-3">
        <div class="form-group mb-1">
            <button type="submit" class="form-control text-center text-white fw-600 border-0 p-0 btn-submit font-xss">
                {{ __('Register') }}
            </button>
        </div>
    </div>
</form>
