# Blade Migration Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Convert the Laravel API app from JSON/Sanctum to full server-rendered Blade views using the Sociala HTML template, with session-based auth.

**Architecture:** Drop Sanctum entirely, switch to Laravel session auth (`web` guard). All controllers return `view()` or `redirect()`. Template HTML is split into Blade layouts + page views. Assets are already in `public/`.

**Tech Stack:** Laravel 13, Blade templating, PHP 8.3, SQLite, session cookies, Laravel built-in auth middleware.

---

## File Map

### Created
- `resources/views/layouts/app.blade.php` — nav + sidebar + footer master layout
- `resources/views/layouts/guest.blade.php` — minimal layout for auth pages
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/auth/forgot.blade.php`
- `resources/views/feed/index.blade.php`
- `resources/views/members/index.blade.php`
- `resources/views/profile/show.blade.php`
- `resources/views/profile/edit.blade.php`
- `resources/views/settings/index.blade.php`
- `resources/views/notifications/index.blade.php`
- `resources/views/messages/index.blade.php`
- `resources/views/groups/index.blade.php`
- `resources/views/groups/show.blade.php`
- `resources/views/admin/analytics.blade.php`
- `resources/views/admin/badges.blade.php`
- `resources/views/admin/users.blade.php`
- `resources/views/admin/user-show.blade.php`
- `resources/views/admin/categories.blade.php`
- `resources/views/admin/moderators.blade.php`
- `resources/views/errors/404.blade.php`
- `app/Http/Controllers/StaticController.php`
- `app/Http/Controllers/MemberController.php`

### Modified
- `app/Http/Controllers/AuthController.php` — session auth, return views/redirects
- `app/Http/Controllers/PublicationController.php` — return views/redirects
- `app/Http/Controllers/CommentController.php` — return redirects
- `app/Http/Controllers/ProfileController.php` — return views/redirects
- `app/Http/Controllers/ReactionController.php` — return redirects
- `app/Http/Controllers/ReportController.php` — return views/redirects
- `app/Http/Controllers/Admin/UserController.php` — return views/redirects
- `app/Http/Controllers/Admin/CategoryController.php` — return views/redirects
- `app/Http/Controllers/Admin/ModeratorController.php` — return views/redirects
- `app/Http/Controllers/Admin/StatisticsController.php` — return views
- `app/Http/Middleware/IsAdmin.php` — return redirect instead of JSON 403
- `app/Http/Middleware/IsModerator.php` — return redirect instead of JSON 403
- `app/Models/User.php` — remove `HasApiTokens` trait
- `routes/web.php` — full route table
- `routes/api.php` — empty
- `bootstrap/app.php` — remove Sanctum middleware alias, remove api route
- `config/auth.php` — remove sanctum guard references

### Removed (via composer)
- `laravel/sanctum` package

---

## Task 1: Remove Sanctum

**Files:**
- Modify: `app/Models/User.php`
- Modify: `bootstrap/app.php`
- Modify: `config/auth.php`
- Modify: `routes/api.php`

- [ ] **Step 1: Remove Sanctum package**

```bash
cd app
composer remove laravel/sanctum
```

Expected: composer.json no longer lists `laravel/sanctum`, vendor updated.

- [ ] **Step 2: Remove HasApiTokens from User model**

In `app/Models/User.php`, remove the import and trait usage:

```php
<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['email', 'password', 'status', 'anonymous_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function moderator(): HasOne
    {
        return $this->hasOne(Moderator::class);
    }

    public function bans(): HasMany
    {
        return $this->hasMany(Ban::class);
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(UserWarning::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class, 'author_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function isAdmin(): bool
    {
        return $this->admin()->exists();
    }

    public function isModerator(): bool
    {
        return $this->moderator()->exists();
    }
}
```

- [ ] **Step 3: Clean up bootstrap/app.php**

Replace `bootstrap/app.php` with:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role.admin' => \App\Http\Middleware\IsAdmin::class,
            'role.mod'   => \App\Http\Middleware\IsModerator::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

- [ ] **Step 4: Empty api.php**

Replace `routes/api.php` with:

```php
<?php
// API routes removed — app uses Blade/session auth.
```

- [ ] **Step 5: Verify app boots**

```bash
php artisan config:clear && php artisan route:list
```

Expected: no errors, only web routes listed (just the default `/` for now).

- [ ] **Step 6: Commit**

```bash
git add app/Models/User.php bootstrap/app.php routes/api.php composer.json composer.lock
git commit -m "chore: remove sanctum, switch to session auth"
```

---

## Task 2: Update Middleware

**Files:**
- Modify: `app/Http/Middleware/IsAdmin.php`
- Modify: `app/Http/Middleware/IsModerator.php`

- [ ] **Step 1: Update IsAdmin to redirect instead of JSON 403**

Replace `app/Http/Middleware/IsAdmin.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}
```

- [ ] **Step 2: Update IsModerator**

Replace `app/Http/Middleware/IsModerator.php` with:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsModerator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isModerator() && ! $request->user()?->isAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Middleware/IsAdmin.php app/Http/Middleware/IsModerator.php
git commit -m "feat: update middleware to use abort() instead of JSON responses"
```

---

## Task 3: Blade Layouts

**Files:**
- Create: `resources/views/layouts/app.blade.php`
- Create: `resources/views/layouts/guest.blade.php`

The layouts are extracted from `template/default.html` (app layout) and `template/login.html` (guest layout). All `href="css/..."` become `{{ asset('css/...') }}`, `src="js/..."` become `{{ asset('js/...') }}`, `src="images/..."` become `{{ asset('images/...') }}`.

- [ ] **Step 1: Create the app layout**

Create `resources/views/layouts/app.blade.php` — this is the full nav+sidebar+footer shell from `template/default.html`, with `@yield('content')` in the main content area and `@yield('scripts')` before `</body>`.

Copy everything from `template/default.html` from `<!DOCTYPE html>` up to (and including) the opening of the main content wrapper, insert `@yield('content')`, then copy the sidebar and footer closing HTML, add `@yield('scripts')`, then the script tags.

Key changes:
- `href="css/style.css"` → `href="{{ asset('css/style.css') }}"`  
- `href="css/themify-icons.css"` → `href="{{ asset('css/themify-icons.css') }}"`
- `href="css/feather.css"` → `href="{{ asset('css/feather.css') }}"`
- `href="css/emoji.css"` → `href="{{ asset('css/emoji.css') }}"`
- `href="css/lightbox.css"` → `href="{{ asset('css/lightbox.css') }}"`
- `src="images/..."` → `src="{{ asset('images/...') }}"` (all occurrences)
- `src="js/plugin.js"` → `src="{{ asset('js/plugin.js') }}"`
- `src="js/scripts.js"` → `src="{{ asset('js/scripts.js') }}"`
- Nav links: `href="default.html"` → `href="{{ route('feed.index') }}"`, `href="default-member.html"` → `href="{{ route('members.index') }}"` etc. (update after routes defined in Task 5)
- Add `@auth` / `@guest` blocks where nav shows user info vs login buttons
- Add logout form in nav:
```html
<form method="POST" action="{{ route('logout') }}" style="display:inline">
    @csrf
    <button type="submit" class="...">Logout</button>
</form>
```

Full structure:
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sociala</title>
    <link rel="stylesheet" href="{{ asset('css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/emoji.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lightbox.css') }}">
    @yield('styles')
</head>
<body class="color-theme-blue mont-font">
    <div class="preloader"></div>
    <div class="main-wrapper">
        {{-- nav-header from template/default.html --}}
        {{-- nav left/sidebar from template/default.html --}}
        <div class="main-content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
        {{-- right sidebar from template/default.html --}}
    </div>
    <script src="{{ asset('js/plugin.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
    @yield('scripts')
</body>
</html>
```

- [ ] **Step 2: Create the guest layout**

Create `resources/views/layouts/guest.blade.php` — the minimal shell from `template/login.html` (just head + body wrapper, no nav/sidebar).

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sociala</title>
    <link rel="stylesheet" href="{{ asset('css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="color-theme-blue">
    <div class="preloader"></div>
    <div class="main-wrap">
        @yield('content')
    </div>
    <script src="{{ asset('js/plugin.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
    @yield('scripts')
</body>
</html>
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/layouts/
git commit -m "feat: add app and guest blade layouts from Sociala template"
```

---

## Task 4: Auth Views

**Files:**
- Create: `resources/views/auth/login.blade.php`
- Create: `resources/views/auth/register.blade.php`
- Create: `resources/views/auth/forgot.blade.php`

- [ ] **Step 1: Create login view**

Create `resources/views/auth/login.blade.php`. Extract the main login form content from `template/login.html` (the `col-xl-7` div with the form). Wire the form to POST `/login`:

```blade
@extends('layouts.guest')

@section('content')
<div class="row">
    <div class="col-xl-5 d-none d-xl-block p-0 vh-100 bg-image-cover bg-no-repeat"
         style="background-image: url({{ asset('images/login-bg.jpg') }});"></div>
    <div class="col-xl-7 vh-100 align-items-center d-flex bg-white rounded-3 overflow-hidden">
        <div class="card shadow-none border-0 ms-auto me-auto login-card">
            <div class="card-body rounded-0 text-left">
                <h2 class="fw-700 display1-size display2-md-size mb-3">Login into <br>your account</h2>

                @if($errors->any())
                    <div class="alert alert-danger font-xsss">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group icon-input mb-3">
                        <i class="font-sm ti-email text-grey-500 pe-0"></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="style2-input ps-5 form-control text-grey-900 font-xsss fw-600"
                               placeholder="Your Email Address" required>
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
                            <button type="submit" class="form-control text-center style2-input text-white fw-600 bg-dark border-0 p-0">Login</button>
                        </div>
                        <h6 class="text-grey-500 font-xsss fw-500 mt-0 mb-0 lh-32">
                            Don't have account <a href="{{ route('register') }}" class="fw-700 ms-1">Register</a>
                        </h6>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 2: Create register view**

Create `resources/views/auth/register.blade.php`. Extract from `template/register.html`:

```blade
@extends('layouts.guest')

@section('content')
<div class="row">
    <div class="col-xl-5 d-none d-xl-block p-0 vh-100 bg-image-cover bg-no-repeat"
         style="background-image: url({{ asset('images/login-bg-2.jpg') }});"></div>
    <div class="col-xl-7 vh-100 align-items-center d-flex bg-white rounded-3 overflow-hidden">
        <div class="card shadow-none border-0 ms-auto me-auto login-card">
            <div class="card-body rounded-0 text-left">
                <h2 class="fw-700 display1-size display2-md-size mb-4">Create <br>your account</h2>

                @if($errors->any())
                    <div class="alert alert-danger font-xsss">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group icon-input mb-3">
                        <i class="font-sm ti-user text-grey-500 pe-0"></i>
                        <input type="text" name="display_name" value="{{ old('display_name') }}"
                               class="style2-input ps-5 form-control text-grey-900 font-xsss fw-600"
                               placeholder="Your Name">
                    </div>
                    <div class="form-group icon-input mb-3">
                        <i class="font-sm ti-email text-grey-500 pe-0"></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="style2-input ps-5 form-control text-grey-900 font-xsss fw-600"
                               placeholder="Your Email Address" required>
                    </div>
                    <div class="form-group icon-input mb-3">
                        <input type="password" name="password"
                               class="style2-input ps-5 form-control text-grey-900 font-xss ls-3"
                               placeholder="Password" required>
                        <i class="font-sm ti-lock text-grey-500 pe-0"></i>
                    </div>
                    <div class="form-group icon-input mb-1">
                        <input type="password" name="password_confirmation"
                               class="style2-input ps-5 form-control text-grey-900 font-xss ls-3"
                               placeholder="Confirm Password" required>
                        <i class="font-sm ti-lock text-grey-500 pe-0"></i>
                    </div>
                    <div class="col-sm-12 p-0 text-left mt-3">
                        <div class="form-group mb-1">
                            <button type="submit" class="form-control text-center style2-input text-white fw-600 bg-dark border-0 p-0">Register</button>
                        </div>
                        <h6 class="text-grey-500 font-xsss fw-500 mt-0 mb-0 lh-32">
                            Already have account <a href="{{ route('login') }}" class="fw-700 ms-1">Login</a>
                        </h6>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 3: Create forgot view**

Create `resources/views/auth/forgot.blade.php`. Extract from `template/forgot.html`:

```blade
@extends('layouts.guest')

@section('content')
<div class="row">
    <div class="col-xl-5 d-none d-xl-block p-0 vh-100 bg-image-cover bg-no-repeat"
         style="background-image: url({{ asset('images/login-bg.jpg') }});"></div>
    <div class="col-xl-7 vh-100 align-items-center d-flex bg-white rounded-3 overflow-hidden">
        <div class="card shadow-none border-0 ms-auto me-auto login-card">
            <div class="card-body rounded-0 text-left">
                <h2 class="fw-700 display1-size display2-md-size mb-3">Forgot <br>your password?</h2>
                <p class="font-xsss text-grey-500 fw-500">Enter your email and we'll send you a link.</p>
                <div class="form-group icon-input mb-3">
                    <i class="font-sm ti-email text-grey-500 pe-0"></i>
                    <input type="email" class="style2-input ps-5 form-control text-grey-900 font-xsss fw-600"
                           placeholder="Your Email Address">
                </div>
                <div class="col-sm-12 p-0 text-left">
                    <div class="form-group mb-1">
                        <a href="{{ route('login') }}" class="form-control text-center style2-input text-white fw-600 bg-dark border-0 p-0">
                            Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 4: Commit**

```bash
git add resources/views/auth/
git commit -m "feat: add auth blade views (login, register, forgot)"
```

---

## Task 5: AuthController + Routes (Auth)

**Files:**
- Modify: `app/Http/Controllers/AuthController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Rewrite AuthController**

Replace `app/Http/Controllers/AuthController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The provided credentials are incorrect.'])->withInput();
        }

        if (Auth::user()->status === 'banned') {
            Auth::logout();
            $request->session()->invalidate();
            return back()->withErrors(['email' => 'Your account has been banned.'])->withInput();
        }

        $request->session()->regenerate();

        return redirect()->intended(route('feed.index'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'display_name' => 'nullable|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'email'    => $data['email'],
            'password' => $data['password'],
            'status'   => 'active',
        ]);

        $user->profile()->create([
            'display_name' => $data['display_name'] ?? explode('@', $data['email'])[0],
        ]);

        Auth::login($user);

        return redirect()->route('feed.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
```

- [ ] **Step 2: Write web.php with all routes**

Replace `routes/web.php` entirely:

```php
<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ModeratorController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaticController;
use Illuminate\Support\Facades\Route;

// ── Guest ────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
    Route::get('/forgot-password', [StaticController::class, 'forgot'])->name('forgot-password');
});

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Feed
    Route::get('/', [PublicationController::class, 'index'])->name('feed.index');
    Route::post('/publications', [PublicationController::class, 'store'])->name('publications.store');
    Route::delete('/publications/{publication}', [PublicationController::class, 'destroy'])->name('publications.destroy');

    // Comments
    Route::post('/publications/{publication}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Reactions
    Route::post('/contents/{content}/reactions', [ReactionController::class, 'toggle'])->name('reactions.toggle');

    // Reports
    Route::post('/contents/{content}/reports', [ReportController::class, 'store'])->name('reports.store');

    // Members
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');

    // Profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

    // Static placeholders
    Route::get('/settings', [StaticController::class, 'settings'])->name('settings.index');
    Route::get('/notifications', [StaticController::class, 'notifications'])->name('notifications.index');
    Route::get('/messages', [StaticController::class, 'messages'])->name('messages.index');
    Route::get('/groups', [StaticController::class, 'groups'])->name('groups.index');
    Route::get('/groups/{id}', [StaticController::class, 'groupShow'])->name('groups.show');

    // ── Moderator ────────────────────────────────────────────────────────────
    Route::middleware('role.mod')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::patch('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    });

    // ── Admin ─────────────────────────────────────────────────────────────────
    Route::middleware('role.admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
        Route::delete('/users/{user}/ban', [UserController::class, 'unban'])->name('users.unban');
        Route::post('/users/{user}/warn', [UserController::class, 'warn'])->name('users.warn');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/moderators', [ModeratorController::class, 'index'])->name('moderators.index');
        Route::post('/moderators', [ModeratorController::class, 'assign'])->name('moderators.assign');
        Route::delete('/moderators/{user}', [ModeratorController::class, 'remove'])->name('moderators.remove');

        Route::get('/analytics', [StatisticsController::class, 'index'])->name('analytics.index');
        Route::post('/statistics/snapshot', [StatisticsController::class, 'snapshot'])->name('statistics.snapshot');
        Route::get('/badges', [StaticController::class, 'badges'])->name('badges.index');
    });
});
```

- [ ] **Step 3: Verify routes load**

```bash
php artisan route:list
```

Expected: all routes listed with correct names and middleware, no errors.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/AuthController.php routes/web.php
git commit -m "feat: session auth controller and full web routes"
```

---

## Task 6: StaticController + MemberController

**Files:**
- Create: `app/Http/Controllers/StaticController.php`
- Create: `app/Http/Controllers/MemberController.php`

- [ ] **Step 1: Create StaticController**

Create `app/Http/Controllers/StaticController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class StaticController extends Controller
{
    public function forgot(): View
    {
        return view('auth.forgot');
    }

    public function settings(): View
    {
        return view('settings.index');
    }

    public function notifications(): View
    {
        return view('notifications.index');
    }

    public function messages(): View
    {
        return view('messages.index');
    }

    public function groups(): View
    {
        return view('groups.index');
    }

    public function groupShow(): View
    {
        return view('groups.show');
    }

    public function badges(): View
    {
        return view('admin.badges');
    }
}
```

- [ ] **Step 2: Create MemberController**

Create `app/Http/Controllers/MemberController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(): View
    {
        $members = User::with('profile')
            ->where('status', 'active')
            ->latest()
            ->paginate(20);

        return view('members.index', compact('members'));
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/StaticController.php app/Http/Controllers/MemberController.php
git commit -m "feat: add StaticController and MemberController"
```

---

## Task 7: PublicationController + CommentController

**Files:**
- Modify: `app/Http/Controllers/PublicationController.php`
- Modify: `app/Http/Controllers/CommentController.php`

- [ ] **Step 1: Rewrite PublicationController**

Replace `app/Http/Controllers/PublicationController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicationController extends Controller
{
    public function index(): View
    {
        $publications = Publication::with(['author.profile', 'category'])
            ->where('contents.status', 'visible')
            ->latest('contents.created_at')
            ->paginate(20);

        return view('feed.index', compact('publications'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'text'        => 'required|string',
            'media_type'  => 'nullable|string|in:image,video,audio,document',
            'category_id' => 'nullable|uuid|exists:categories,id',
        ]);

        $content = Content::create([
            'type'      => 'publication',
            'status'    => 'visible',
            'author_id' => $request->user()->id,
        ]);

        DB::table('publications')->insert(array_merge([
            'id'         => $content->id,
            'created_at' => now(),
            'updated_at' => now(),
        ], $data));

        return redirect()->route('feed.index')->with('success', 'Publication posted.');
    }

    public function destroy(Request $request, Publication $publication): RedirectResponse
    {
        if ($request->user()->id !== $publication->author_id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        Content::where('id', $publication->id)->update(['status' => 'deleted']);

        return redirect()->route('feed.index')->with('success', 'Publication deleted.');
    }
}
```

- [ ] **Step 2: Rewrite CommentController**

Replace `app/Http/Controllers/CommentController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Content;
use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Publication $publication): RedirectResponse
    {
        $data = $request->validate([
            'text'      => 'required|string',
            'parent_id' => 'nullable|uuid|exists:comments,id',
        ]);

        $content = Content::create([
            'type'      => 'comment',
            'status'    => 'visible',
            'author_id' => $request->user()->id,
        ]);

        Comment::create([
            'id'             => $content->id,
            'text'           => $data['text'],
            'publication_id' => $publication->id,
            'parent_id'      => $data['parent_id'] ?? null,
        ]);

        return back()->with('success', 'Comment posted.');
    }

    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        if ($request->user()->id !== $comment->author_id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        Content::where('id', $comment->id)->update(['status' => 'deleted']);

        return back()->with('success', 'Comment deleted.');
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/PublicationController.php app/Http/Controllers/CommentController.php
git commit -m "feat: convert publication and comment controllers to blade/redirect"
```

---

## Task 8: ProfileController + ReactionController + ReportController

**Files:**
- Modify: `app/Http/Controllers/ProfileController.php`
- Modify: `app/Http/Controllers/ReactionController.php`
- Modify: `app/Http/Controllers/ReportController.php`

- [ ] **Step 1: Rewrite ProfileController**

Replace `app/Http/Controllers/ProfileController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        $user->load('profile');
        return view('profile.show', compact('user'));
    }

    public function edit(Request $request): View
    {
        $user = $request->user()->load('profile');
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'display_name' => 'sometimes|string|max:255',
            'bio'          => 'sometimes|nullable|string',
            'avatar_url'   => 'sometimes|nullable|url',
        ]);

        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return back()->with('success', 'Profile updated.');
    }
}
```

- [ ] **Step 2: Rewrite ReactionController**

Replace `app/Http/Controllers/ReactionController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Reaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function toggle(Request $request, Content $content): RedirectResponse
    {
        $data = $request->validate([
            'type' => 'required|string',
        ]);

        $existing = Reaction::where('user_id', $request->user()->id)
            ->where('content_id', $content->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            Reaction::create([
                'user_id'    => $request->user()->id,
                'content_id' => $content->id,
                'type'       => $data['type'],
            ]);
        }

        return back();
    }
}
```

- [ ] **Step 3: Rewrite ReportController**

Replace `app/Http/Controllers/ReportController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = Report::with(['content', 'reporter.profile'])
            ->latest()
            ->paginate(20);

        return view('moderator.reports', compact('reports'));
    }

    public function store(Request $request, Content $content): RedirectResponse
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        Report::create([
            'content_id'  => $content->id,
            'reporter_id' => $request->user()->id,
            'reason'      => $data['reason'],
        ]);

        return back()->with('success', 'Report submitted.');
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,reviewed,dismissed',
        ]);

        $report->update($data);

        return back()->with('success', 'Report updated.');
    }
}
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/ProfileController.php app/Http/Controllers/ReactionController.php app/Http/Controllers/ReportController.php
git commit -m "feat: convert profile, reaction, report controllers to blade/redirect"
```

---

## Task 9: Admin Controllers

**Files:**
- Modify: `app/Http/Controllers/Admin/UserController.php`
- Modify: `app/Http/Controllers/Admin/CategoryController.php`
- Modify: `app/Http/Controllers/Admin/ModeratorController.php`
- Modify: `app/Http/Controllers/Admin/StatisticsController.php`

- [ ] **Step 1: Rewrite Admin\UserController**

Replace `app/Http/Controllers/Admin/UserController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ban;
use App\Models\User;
use App\Models\UserWarning;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $members = User::with('profile')->latest()->paginate(20);
        return view('admin.users', compact('members'));
    }

    public function show(User $user): View
    {
        $user->load('profile', 'bans', 'warnings');
        return view('admin.user-show', compact('user'));
    }

    public function ban(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);

        $user->update(['status' => 'banned']);
        Ban::create([
            'user_id'    => $user->id,
            'banned_by'  => $request->user()->id,
            'reason'     => $data['reason'],
        ]);

        return back()->with('success', 'User banned.');
    }

    public function unban(User $user): RedirectResponse
    {
        $user->update(['status' => 'active']);
        return back()->with('success', 'User unbanned.');
    }

    public function warn(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);

        UserWarning::create([
            'user_id'    => $user->id,
            'warned_by'  => $request->user()->id,
            'reason'     => $data['reason'],
        ]);

        return back()->with('success', 'Warning issued.');
    }
}
```

- [ ] **Step 2: Rewrite Admin\CategoryController**

Replace `app/Http/Controllers/Admin/CategoryController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::latest()->get();
        return view('admin.categories', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255|unique:categories,name']);
        Category::create($data);
        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255|unique:categories,name,' . $category->id]);
        $category->update($data);
        return back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
```

- [ ] **Step 3: Rewrite Admin\ModeratorController**

Replace `app/Http/Controllers/Admin/ModeratorController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Moderator;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModeratorController extends Controller
{
    public function index(): View
    {
        $moderators = Moderator::with('user.profile')->get();
        return view('admin.moderators', compact('moderators'));
    }

    public function assign(Request $request): RedirectResponse
    {
        $data = $request->validate(['user_id' => 'required|uuid|exists:users,id']);
        Moderator::firstOrCreate(['user_id' => $data['user_id']]);
        return back()->with('success', 'Moderator assigned.');
    }

    public function remove(User $user): RedirectResponse
    {
        Moderator::where('user_id', $user->id)->delete();
        return back()->with('success', 'Moderator removed.');
    }
}
```

- [ ] **Step 4: Rewrite Admin\StatisticsController**

Replace `app/Http/Controllers/Admin/StatisticsController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformStatistics;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(): View
    {
        $stats = PlatformStatistics::latest()->first();
        return view('admin.analytics', compact('stats'));
    }

    public function snapshot(): RedirectResponse
    {
        PlatformStatistics::create([
            'total_users'        => \App\Models\User::count(),
            'total_publications' => \App\Models\Publication::count(),
            'total_comments'     => \App\Models\Comment::count(),
            'total_reports'      => \App\Models\Report::count(),
        ]);

        return back()->with('success', 'Snapshot taken.');
    }
}
```

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Admin/
git commit -m "feat: convert admin controllers to blade/redirect"
```

---

## Task 10: Feed View

**Files:**
- Create: `resources/views/feed/index.blade.php`

- [ ] **Step 1: Create feed view**

Create `resources/views/feed/index.blade.php`. Extract the main content column (center column with post cards) from `template/default.html`. Replace static post HTML with `@foreach($publications as $publication)` loop:

```blade
@extends('layouts.app')

@section('content')
{{-- Post creation form --}}
<div class="card w-100 shadow-xss rounded-xxl border-0 ps-4 pt-4 pe-4 pb-3 mb-3">
    <form method="POST" action="{{ route('publications.store') }}">
        @csrf
        <div class="card-body p-0">
            <input type="text" name="title"
                   class="form-control bor-0 w-100 rounded-xxl font-xssss fw-600 bg-greylight border-0"
                   placeholder="Publication title..." required>
        </div>
        <div class="card-body p-0 mt-2">
            <textarea name="text" rows="3"
                      class="form-control bor-0 w-100 rounded-xxl font-xssss fw-600 bg-greylight border-0"
                      placeholder="What's on your mind?" required></textarea>
        </div>
        <div class="card-body d-flex p-0 mt-0">
            <button type="submit" class="p-2 lh-20 w100 bg-current me-2 text-white text-center font-xssss fw-600 ls-1 rounded-xl border-0">
                Post
            </button>
        </div>
    </form>
</div>

{{-- Publications feed --}}
@foreach($publications as $publication)
<div class="card w-100 shadow-xss rounded-xxl border-0 p-4 mb-3">
    <div class="card-body p-0 d-flex">
        <figure class="avatar me-3">
            <img src="{{ $publication->author->profile->avatar_url ?? asset('images/profile-4.png') }}"
                 alt="user" class="shadow-sm rounded-circle w45">
        </figure>
        <h4 class="fw-700 text-grey-900 font-xssss mt-1">
            {{ $publication->author->profile->display_name ?? $publication->author->email }}
            <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">
                {{ $publication->created_at->diffForHumans() }}
            </span>
        </h4>
        @if(auth()->id() === $publication->author_id || auth()->user()->isAdmin())
        <form method="POST" action="{{ route('publications.destroy', $publication->id) }}" class="ms-auto">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm text-danger">
                <i class="feather-trash"></i>
            </button>
        </form>
        @endif
    </div>
    <div class="card-body p-0 me-lg-5">
        <h5 class="fw-600 text-grey-900 font-xss mt-2 mb-1">{{ $publication->title }}</h5>
        <p class="fw-500 text-grey-500 lh-26 font-xssss w-100 mb-2">{{ $publication->text }}</p>
    </div>

    {{-- Reaction form --}}
    <div class="card-body d-flex p-0">
        <form method="POST" action="{{ route('reactions.toggle', $publication->id) }}">
            @csrf
            <input type="hidden" name="type" value="like">
            <button type="submit" class="emoji-bttn d-flex align-items-center fw-600 text-grey-900 text-dark lh-26 font-xssss me-2 border-0 bg-transparent">
                <i class="feather-thumbs-up text-white bg-primary-gradiant me-1 btn-round-xs font-xss"></i>
                <i class="feather-heart text-white bg-red-gradiant me-2 btn-round-xs font-xss"></i>
                Like
            </button>
        </form>
        <a href="#" class="d-flex align-items-center fw-600 text-grey-900 text-dark lh-26 font-xssss">
            <i class="feather-message-circle text-dark text-grey-900 btn-round-sm font-lg"></i>
            <span class="d-none-xss">Comment</span>
        </a>
    </div>

    {{-- Comment form --}}
    <div class="card-body p-0 mt-3 position-relative">
        <form method="POST" action="{{ route('comments.store', $publication->id) }}">
            @csrf
            <input type="text" name="text"
                   class="bor-0 w-100 rounded-xxl font-xssss fw-500 ps-4 pe-4 pt-2 pb-2 bg-greylight theme-dark-bg border-0"
                   placeholder="Write a comment..." required>
        </form>
    </div>
</div>
@endforeach

{{-- Pagination --}}
<div class="d-flex justify-content-center mt-3">
    {{ $publications->links() }}
</div>
@endsection
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/feed/
git commit -m "feat: add feed blade view with publications loop"
```

---

## Task 11: Profile Views

**Files:**
- Create: `resources/views/profile/show.blade.php`
- Create: `resources/views/profile/edit.blade.php`

- [ ] **Step 1: Create profile show view**

Create `resources/views/profile/show.blade.php`. Extract from `template/author-page.html`:

```blade
@extends('layouts.app')

@section('content')
<div class="card w-100 border-0 p-0 bg-white shadow-xss rounded-xxl">
    <div class="card-body h250 p-0 rounded-xxl overflow-hidden m-3">
        <img src="{{ asset('images/bb-9.jpg') }}" alt="cover" class="w-100">
    </div>
    <div class="card-body p-0 position-relative">
        <figure class="avatar position-absolute w75 z-index-1"
                style="top:-40px; left:30px;">
            <img src="{{ $user->profile->avatar_url ?? asset('images/profile-4.png') }}"
                 alt="avatar" class="float-right p-1 bg-white rounded-circle w-100">
        </figure>
        <div class="clearfix"></div>
        <div class="card-body d-block pt-1 pb-4 ps-4 text-left">
            <h4 class="fw-700 font-xssss mt-3 mb-1">
                {{ $user->profile->display_name ?? $user->email }}
            </h4>
            <p class="fw-500 font-xssss text-grey-500 mt-0 mb-3 lh-3">
                {{ $user->profile->bio ?? '' }}
            </p>
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 2: Create profile edit view**

Create `resources/views/profile/edit.blade.php`. Extract from `template/user-page.html`:

```blade
@extends('layouts.app')

@section('content')
<div class="card w-100 border-0 bg-white shadow-xs p-0 mb-4">
    <div class="card-body p-4 w-100 bg-current border-0 d-flex rounded-3">
        <h4 class="font-xssss text-white fw-600 ms-4 mb-0 mt-2">Account Details</h4>
    </div>
    <div class="card-body p-lg-5 p-4 w-100 border-0">

        @if(session('success'))
            <div class="alert alert-success font-xsss">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger font-xsss">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="row">
                <div class="col-lg-6 mb-3">
                    <div class="form-group">
                        <label class="mont-font fw-600 font-xsss">Display Name</label>
                        <input type="text" name="display_name"
                               value="{{ old('display_name', $user->profile->display_name ?? '') }}"
                               class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="form-group">
                        <label class="mont-font fw-600 font-xsss">Email</label>
                        <input type="email" value="{{ $user->email }}"
                               class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 mb-3">
                    <div class="form-group">
                        <label class="mont-font fw-600 font-xsss">Bio</label>
                        <textarea name="bio" rows="4"
                                  class="form-control">{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 mb-3">
                    <div class="form-group">
                        <label class="mont-font fw-600 font-xsss">Avatar URL</label>
                        <input type="url" name="avatar_url"
                               value="{{ old('avatar_url', $user->profile->avatar_url ?? '') }}"
                               class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <button type="submit" class="bg-current text-center text-white font-xsss fw-600 p-3 w175 rounded-3 d-inline-block border-0">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/profile/
git commit -m "feat: add profile show and edit blade views"
```

---

## Task 12: Members View

**Files:**
- Create: `resources/views/members/index.blade.php`

- [ ] **Step 1: Create members view**

Create `resources/views/members/index.blade.php`. Extract member card structure from `template/default-member.html`:

```blade
@extends('layouts.app')

@section('content')
<div class="row">
    @foreach($members as $member)
    <div class="col-md-3 col-sm-4 pe-2 ps-2">
        <div class="card d-block border-0 shadow-xss rounded-3 overflow-hidden mb-3">
            <div class="card-body d-block w-100 ps-3 pe-3 pb-4 text-center">
                <figure class="overflow-hidden avatar ms-auto me-auto mb-0 position-relative w65 z-index-1">
                    <img src="{{ $member->profile->avatar_url ?? asset('images/profile-4.png') }}"
                         alt="avatar" class="float-right p-0 bg-white rounded-circle w-100">
                </figure>
                <div class="clearfix w-100"></div>
                <h4 class="fw-700 font-xsss mt-3 mb-0">
                    {{ $member->profile->display_name ?? $member->email }}
                </h4>
                <p class="fw-500 font-xssss text-grey-500 mt-0 mb-3">
                    {{ $member->profile->bio ?? '' }}
                </p>
                <a href="{{ route('profile.show', $member->id) }}"
                   class="mt-0 btn pt-2 pb-2 ps-3 pe-3 lh-24 ms-1 ls-3 d-inline-block rounded-xl bg-current font-xsssss fw-700 ls-lg text-white">
                    FOLLOW
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $members->links() }}
</div>
@endsection
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/members/
git commit -m "feat: add members blade view"
```

---

## Task 13: Static Placeholder Views

**Files:**
- Create: `resources/views/settings/index.blade.php`
- Create: `resources/views/notifications/index.blade.php`
- Create: `resources/views/messages/index.blade.php`
- Create: `resources/views/groups/index.blade.php`
- Create: `resources/views/groups/show.blade.php`
- Create: `resources/views/admin/badges.blade.php`

- [ ] **Step 1: Create each static view**

Each file extends `layouts.app` and yields the body content from its corresponding template file. Replace all `src="images/..."` with `{{ asset('images/...') }}` and `href="css/..."` (not needed — in layout already).

Create `resources/views/settings/index.blade.php`:
```blade
@extends('layouts.app')
@section('content')
{{-- Paste inner content from template/default-settings.html main column here --}}
<div class="card w-100 border-0 bg-white shadow-xs p-0 mb-4">
    <div class="card-body p-4 w-100 bg-current border-0 d-flex rounded-3">
        <h4 class="font-xssss text-white fw-600 ms-4 mb-0 mt-2">Settings</h4>
    </div>
    <div class="card-body p-lg-5 p-4 w-100 border-0">
        <p class="text-grey-500 font-xsss">Settings coming soon.</p>
    </div>
</div>
@endsection
```

Create `resources/views/notifications/index.blade.php`:
```blade
@extends('layouts.app')
@section('content')
{{-- Paste inner content from template/default-notification.html main column here --}}
<div class="card w-100 shadow-xss rounded-xxl border-0 mb-3 p-4">
    <h4 class="fw-700 font-xss mb-3">Notifications</h4>
    <p class="text-grey-500 font-xsss">No notifications yet.</p>
</div>
@endsection
```

Create `resources/views/messages/index.blade.php`:
```blade
@extends('layouts.app')
@section('content')
{{-- Paste inner content from template/default-message.html main column here --}}
<div class="card w-100 shadow-xss rounded-xxl border-0 mb-3 p-4">
    <h4 class="fw-700 font-xss mb-3">Messages</h4>
    <p class="text-grey-500 font-xsss">No messages yet.</p>
</div>
@endsection
```

Create `resources/views/groups/index.blade.php`:
```blade
@extends('layouts.app')
@section('content')
{{-- Paste inner content from template/default-group.html main column here --}}
<div class="card w-100 shadow-xss rounded-xxl border-0 mb-3 p-4">
    <h4 class="fw-700 font-xss mb-3">Groups</h4>
    <p class="text-grey-500 font-xsss">Groups coming soon.</p>
</div>
@endsection
```

Create `resources/views/groups/show.blade.php`:
```blade
@extends('layouts.app')
@section('content')
{{-- Paste inner content from template/group-page.html main column here --}}
<div class="card w-100 shadow-xss rounded-xxl border-0 mb-3 p-4">
    <h4 class="fw-700 font-xss mb-3">Group</h4>
    <p class="text-grey-500 font-xsss">Group detail coming soon.</p>
</div>
@endsection
```

Create `resources/views/admin/badges.blade.php`:
```blade
@extends('layouts.app')
@section('content')
{{-- Paste inner content from template/default-badge.html main column here --}}
<div class="card w-100 shadow-xss rounded-xxl border-0 mb-3 p-4">
    <h4 class="fw-700 font-xss mb-3">Badges</h4>
    <p class="text-grey-500 font-xsss">Badges coming soon.</p>
</div>
@endsection
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/settings/ resources/views/notifications/ resources/views/messages/ resources/views/groups/ resources/views/admin/badges.blade.php
git commit -m "feat: add static placeholder blade views"
```

---

## Task 14: Admin Views

**Files:**
- Create: `resources/views/admin/analytics.blade.php`
- Create: `resources/views/admin/users.blade.php`
- Create: `resources/views/admin/user-show.blade.php`
- Create: `resources/views/admin/categories.blade.php`
- Create: `resources/views/admin/moderators.blade.php`

- [ ] **Step 1: Create admin/analytics view**

Create `resources/views/admin/analytics.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="card w-100 shadow-xss rounded-xxl border-0 p-4 mb-3">
    <h4 class="fw-700 font-xs mb-4">Platform Analytics</h4>
    @if($stats)
    <div class="row">
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-xss rounded-xxl p-3 text-center">
                <h2 class="fw-700 font-xl text-current">{{ $stats->total_users ?? 0 }}</h2>
                <p class="font-xssss text-grey-500 mb-0">Users</p>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-xss rounded-xxl p-3 text-center">
                <h2 class="fw-700 font-xl text-current">{{ $stats->total_publications ?? 0 }}</h2>
                <p class="font-xssss text-grey-500 mb-0">Publications</p>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-xss rounded-xxl p-3 text-center">
                <h2 class="fw-700 font-xl text-current">{{ $stats->total_comments ?? 0 }}</h2>
                <p class="font-xssss text-grey-500 mb-0">Comments</p>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-xss rounded-xxl p-3 text-center">
                <h2 class="fw-700 font-xl text-current">{{ $stats->total_reports ?? 0 }}</h2>
                <p class="font-xssss text-grey-500 mb-0">Reports</p>
            </div>
        </div>
    </div>
    @else
    <p class="text-grey-500 font-xsss">No snapshot yet.</p>
    @endif

    <form method="POST" action="{{ route('admin.statistics.snapshot') }}" class="mt-3">
        @csrf
        <button type="submit" class="btn bg-current text-white font-xsss fw-600 rounded-3 border-0 p-2 ps-3 pe-3">
            Take Snapshot
        </button>
    </form>
</div>
@endsection
```

- [ ] **Step 2: Create admin/users view**

Create `resources/views/admin/users.blade.php`. Reuse `default-member.html` card structure with admin actions:

```blade
@extends('layouts.app')

@section('content')
<div class="card w-100 shadow-xss rounded-xxl border-0 p-4 mb-3">
    <h4 class="fw-700 font-xs mb-4">User Management</h4>
</div>

@if(session('success'))
    <div class="alert alert-success font-xsss">{{ session('success') }}</div>
@endif

<div class="row">
    @foreach($members as $member)
    <div class="col-md-3 col-sm-4 pe-2 ps-2">
        <div class="card d-block border-0 shadow-xss rounded-3 overflow-hidden mb-3">
            <div class="card-body d-block w-100 ps-3 pe-3 pb-4 text-center">
                <figure class="overflow-hidden avatar ms-auto me-auto mb-0 position-relative w65 z-index-1">
                    <img src="{{ $member->profile->avatar_url ?? asset('images/profile-4.png') }}"
                         alt="avatar" class="float-right p-0 bg-white rounded-circle w-100">
                </figure>
                <h4 class="fw-700 font-xsss mt-3 mb-0">
                    {{ $member->profile->display_name ?? $member->email }}
                </h4>
                <span class="badge {{ $member->status === 'banned' ? 'bg-danger' : 'bg-success' }} font-xsssss mt-1">
                    {{ $member->status }}
                </span>
                <div class="mt-2">
                    <a href="{{ route('admin.users.show', $member->id) }}"
                       class="btn btn-sm bg-current text-white font-xsssss fw-600 rounded-xl border-0 p-1 ps-2 pe-2">
                        View
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $members->links() }}
</div>
@endsection
```

- [ ] **Step 3: Create admin/user-show view**

Create `resources/views/admin/user-show.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="card w-100 border-0 p-0 bg-white shadow-xss rounded-xxl">
    <div class="card-body p-0 position-relative">
        <div class="card-body d-block pt-4 pb-4 ps-4">
            <h4 class="fw-700 font-xssss">{{ $user->profile->display_name ?? $user->email }}</h4>
            <p class="font-xssss text-grey-500">{{ $user->email }}</p>
            <span class="badge {{ $user->status === 'banned' ? 'bg-danger' : 'bg-success' }}">
                {{ $user->status }}
            </span>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success font-xsss mt-3">{{ session('success') }}</div>
@endif

<div class="card w-100 border-0 shadow-xss rounded-xxl p-4 mt-3">
    <h5 class="fw-700 font-xsss mb-3">Actions</h5>
    <div class="d-flex gap-2 flex-wrap">
        @if($user->status !== 'banned')
        <form method="POST" action="{{ route('admin.users.ban', $user->id) }}">
            @csrf
            <input type="hidden" name="reason" value="Admin action">
            <button type="submit" class="btn btn-sm btn-danger font-xssss fw-600">Ban</button>
        </form>
        @else
        <form method="POST" action="{{ route('admin.users.unban', $user->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-success font-xssss fw-600">Unban</button>
        </form>
        @endif

        <form method="POST" action="{{ route('admin.users.warn', $user->id) }}">
            @csrf
            <input type="hidden" name="reason" value="Admin warning">
            <button type="submit" class="btn btn-sm btn-warning font-xssss fw-600">Warn</button>
        </form>
    </div>
</div>

@if($user->bans->count())
<div class="card w-100 border-0 shadow-xss rounded-xxl p-4 mt-3">
    <h5 class="fw-700 font-xsss mb-3">Ban History</h5>
    @foreach($user->bans as $ban)
    <div class="d-flex mb-2">
        <p class="font-xssss text-grey-500 mb-0">{{ $ban->reason }} — {{ $ban->created_at->diffForHumans() }}</p>
    </div>
    @endforeach
</div>
@endif
@endsection
```

- [ ] **Step 4: Create admin/categories view**

Create `resources/views/admin/categories.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="card w-100 border-0 shadow-xss rounded-xxl p-4 mb-3">
    <h4 class="fw-700 font-xs mb-4">Categories</h4>

    @if(session('success'))
        <div class="alert alert-success font-xsss">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.categories.store') }}" class="d-flex gap-2 mb-4">
        @csrf
        <input type="text" name="name" placeholder="Category name"
               class="form-control font-xsss" required>
        <button type="submit" class="btn bg-current text-white font-xsss fw-600 rounded-3 border-0 p-2 ps-3 pe-3">
            Add
        </button>
    </form>

    <ul class="list-group">
        @foreach($categories as $category)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="font-xsss fw-600">{{ $category->name }}</span>
            <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger font-xssss border-0">Delete</button>
            </form>
        </li>
        @endforeach
    </ul>
</div>
@endsection
```

- [ ] **Step 5: Create admin/moderators view**

Create `resources/views/admin/moderators.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="card w-100 border-0 shadow-xss rounded-xxl p-4 mb-3">
    <h4 class="fw-700 font-xs mb-4">Moderators</h4>

    @if(session('success'))
        <div class="alert alert-success font-xsss">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.moderators.assign') }}" class="d-flex gap-2 mb-4">
        @csrf
        <input type="text" name="user_id" placeholder="User UUID"
               class="form-control font-xsss" required>
        <button type="submit" class="btn bg-current text-white font-xsss fw-600 rounded-3 border-0 p-2 ps-3 pe-3">
            Assign
        </button>
    </form>

    <ul class="list-group">
        @foreach($moderators as $moderator)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="font-xsss fw-600">{{ $moderator->user->profile->display_name ?? $moderator->user->email }}</span>
            <form method="POST" action="{{ route('admin.moderators.remove', $moderator->user_id) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger font-xssss border-0">Remove</button>
            </form>
        </li>
        @endforeach
    </ul>
</div>
@endsection
```

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/
git commit -m "feat: add admin blade views (analytics, users, categories, moderators)"
```

---

## Task 15: Error View + Final Wiring

**Files:**
- Create: `resources/views/errors/404.blade.php`

- [ ] **Step 1: Create 404 view**

Create `resources/views/errors/404.blade.php`. Extract from `template/404.html`:

```blade
@extends('layouts.guest')

@section('content')
<div class="main-wrap">
    <div class="nav-header bg-transparent shadow-none border-0">
        <div class="nav-top w-100">
            <a href="{{ route('feed.index') }}">
                <i class="feather-zap text-success display1-size me-2 ms-0"></i>
                <span class="d-inline-block fredoka-font ls-3 fw-600 text-current font-xxl logo-text mb-0">Sociala.</span>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-6 vh-100 align-items-center d-flex bg-white rounded-3 overflow-hidden">
            <div class="card shadow-none border-0 ms-auto me-auto login-card">
                <div class="card-body text-center rounded-0 p-5">
                    <h1 class="fw-700 text-grey-900 display4-size display4-md-size mb-4">Oops! <br>Page not found.</h1>
                    <a href="{{ route('feed.index') }}"
                       class="p-3 w175 bg-current text-white d-inline-block text-center fw-600 font-xssss rounded-3">
                        Return Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 2: Verify the app starts and login page loads**

```bash
php artisan config:clear && php artisan cache:clear && php artisan route:list
```

Then open `http://localhost:8000/login` in browser (start server with `php artisan serve`).

Expected: Sociala login page renders with CSS/JS from `public/`.

- [ ] **Step 3: Test login flow**

- Register a user via `/register`
- Login at `/login`
- Should redirect to `/` (feed)
- Feed page should render (empty or with seeded data)

- [ ] **Step 4: Commit**

```bash
git add resources/views/errors/
git commit -m "feat: add 404 error blade view"
```

---

## Self-Review Notes

**Spec coverage check:**
- ✅ Sanctum removal → Task 1
- ✅ Middleware update → Task 2
- ✅ Layouts → Task 3
- ✅ Auth views → Task 4
- ✅ AuthController + routes → Task 5
- ✅ StaticController + MemberController → Task 6
- ✅ PublicationController + CommentController → Task 7
- ✅ ProfileController + ReactionController + ReportController → Task 8
- ✅ Admin controllers → Task 9
- ✅ Feed view → Task 10
- ✅ Profile views → Task 11
- ✅ Members view → Task 12
- ✅ Static placeholder views → Task 13
- ✅ Admin views → Task 14
- ✅ 404 view → Task 15

**One gap found and fixed:** `ReportController@index` returns `view('moderator.reports', ...)` but no `moderator/reports.blade.php` view is listed. Since there's no moderator template page in the template set, this view needs to be created as a simple placeholder. Added to Task 13 static views scope — add this file:

Create `resources/views/moderator/reports.blade.php`:
```blade
@extends('layouts.app')
@section('content')
<div class="card w-100 shadow-xss rounded-xxl border-0 p-4 mb-3">
    <h4 class="fw-700 font-xs mb-4">Reports</h4>
    @if(session('success'))
        <div class="alert alert-success font-xsss">{{ session('success') }}</div>
    @endif
    @foreach($reports as $report)
    <div class="card border-0 shadow-xss rounded-xxl p-3 mb-2">
        <p class="font-xssss fw-600 mb-1">{{ $report->reason }}</p>
        <p class="font-xssss text-grey-500 mb-1">Reported by: {{ $report->reporter->profile->display_name ?? $report->reporter->email }}</p>
        <form method="POST" action="{{ route('reports.update', $report->id) }}" class="d-inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="reviewed">
            <button type="submit" class="btn btn-sm bg-current text-white font-xssss border-0">Mark Reviewed</button>
        </form>
    </div>
    @endforeach
    <div class="d-flex justify-content-center mt-3">{{ $reports->links() }}</div>
</div>
@endsection
```

Add this to Task 13 Step 1 before committing.
