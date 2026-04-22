# Blade Migration Design

**Date:** 2026-04-22  
**Scope:** Convert Laravel API (Sanctum/JSON) to full Blade SSR app using the Sociala HTML template.

---

## Goal

Replace all JSON API responses and Sanctum token auth with server-rendered Blade views using session-based auth. Assets (CSS, JS, images, fonts) are already copied to `public/`.

---

## Architecture

- Single `web.php` routes file — no `api.php` routes remain active.
- Session auth using Laravel's built-in `web` guard (`Auth::attempt`, `Auth::login`, `Auth::logout`).
- Sanctum removed entirely (`composer remove laravel/sanctum`, delete its migration, remove from config).
- Controllers return `view()` or `redirect()` — no `JsonResponse` anywhere.
- Form submissions: `@csrf` POST, redirect on success/failure using `withErrors()` / `with()` flash.
- Middleware: built-in `auth` (protected routes), `guest` (login/register), existing `IsAdmin` / `IsModerator` updated to use session auth.

---

## Layouts

Two master layouts extracted from the template's repeated HTML:

- `resources/views/layouts/app.blade.php` — full layout with nav, sidebar, footer. Used by all authenticated pages.
- `resources/views/layouts/guest.blade.php` — minimal layout (no nav/sidebar). Used by login, register, forgot-password.

All views `@extend` one of these layouts and fill `@section('content')`.

Asset references updated from `href="css/..."` to `{{ asset('css/...') }}` etc.

---

## Page Mapping

| Template file | Blade view path | Route | Guard |
|---|---|---|---|
| `login.html` | `auth.login` | `GET /login` | guest |
| `register.html` | `auth.register` | `GET /register` | guest |
| `forgot.html` | `auth.forgot` | `GET /forgot-password` | guest |
| `default.html` | `feed.index` | `GET /` | auth |
| `default-member.html` | `members.index` | `GET /members` | auth |
| `author-page.html` | `profile.show` | `GET /profile/{user}` | auth |
| `user-page.html` | `profile.edit` | `GET /profile/edit` | auth |
| `default-settings.html` | `settings.index` | `GET /settings` | auth |
| `default-notification.html` | `notifications.index` | `GET /notifications` | auth (static) |
| `default-message.html` | `messages.index` | `GET /messages` | auth (static) |
| `default-group.html` | `groups.index` | `GET /groups` | auth (static) |
| `group-page.html` | `groups.show` | `GET /groups/{id}` | auth (static) |
| `default-analytics.html` | `admin.analytics` | `GET /admin/analytics` | IsAdmin |
| `default-badge.html` | `admin.badges` | `GET /admin/badges` | IsAdmin (static) |
| `404.html` | `errors.404` | auto | — |

---

## Controllers

### AuthController
Methods: `showLogin`, `login`, `showRegister`, `register`, `logout`

- `login`: validate → `Auth::attempt()` → check banned status → regenerate session → redirect `/`
- `register`: validate → create User + Profile → `Auth::login()` → redirect `/`
- `logout`: `Auth::logout()` → invalidate session → redirect `/login`

### PublicationController
- `index`: paginated publications with author+profile+category → `view('feed.index', compact('publications'))`
- `store`: validate → create Content + Publication → redirect back
- `destroy`: ownership check → soft-delete (set status=deleted) → redirect back

### CommentController
- `store`: validate → create Content + Comment → redirect back
- `destroy`: ownership/admin check → soft-delete → redirect back

### ProfileController
- `show(User $user)`: load user+profile → `view('profile.show', ...)`
- `edit`: load auth user+profile → `view('profile.edit', ...)`
- `update`: validate → updateOrCreate profile → redirect back

### ReactionController
- `toggle`: find/create or delete reaction → redirect back

### ReportController
- `store`: validate → create report → redirect back with flash

### Admin\UserController
- `index`: paginated users → `view('admin.users', ...)` using `default-member.html` layout
- `show(User $user)`: → `view('admin.user-show', ...)` using `author-page.html` layout
- `ban`, `unban`, `warn`: POST actions → redirect back

### Admin\CategoryController
- `index`: all categories → `view('admin.categories', ...)` 
- `store`, `update`, `destroy`: POST/PATCH/DELETE → redirect back

### Admin\ModeratorController
- `index`: all moderators → `view('admin.moderators', ...)`
- `assign`, `remove`: POST/DELETE → redirect back

### Admin\StatisticsController
- `index`: latest stats → `view('admin.analytics', ...)` using `default-analytics.html` layout

### MemberController (new)
- `index`: paginated users with profiles → `view('members.index', compact('members'))` using `default-member.html` layout

### StaticController (new)
Handles all static placeholder pages: `notifications`, `messages`, `groups`, `groupShow`, `badges`, `forgot`.
Each method returns its view. No data passed.

---

## Middleware Updates

- `IsAdmin`: remove `auth:sanctum` check, use `Auth::check()` + `$request->user()->isAdmin()`
- `IsModerator`: same pattern
- Route groups: replace `auth:sanctum` with `auth`

---

## Routes Structure (web.php)

```
Guest:
  GET  /login              AuthController@showLogin
  POST /login              AuthController@login
  GET  /register           AuthController@showRegister
  POST /register           AuthController@register
  GET  /forgot-password    StaticController@forgot

Auth:
  POST /logout             AuthController@logout

  GET  /                   PublicationController@index
  POST /publications       PublicationController@store
  DELETE /publications/{p} PublicationController@destroy

  GET  /publications/{p}/comments   CommentController@index (unused — comments on feed page)
  POST /publications/{p}/comments   CommentController@store
  DELETE /comments/{c}              CommentController@destroy

  POST /contents/{c}/reactions      ReactionController@toggle
  POST /contents/{c}/reports        ReportController@store

  GET  /members            MemberController@index
  GET  /profile/edit       ProfileController@edit
  PATCH /profile           ProfileController@update
  GET  /profile/{user}     ProfileController@show

  GET  /settings           StaticController@settings
  GET  /notifications      StaticController@notifications
  GET  /messages           StaticController@messages
  GET  /groups             StaticController@groups
  GET  /groups/{id}        StaticController@groupShow

Moderator:
  GET  /reports            ReportController@index
  PATCH /reports/{r}       ReportController@update

Admin (prefix /admin):
  GET  /users              Admin\UserController@index
  GET  /users/{u}          Admin\UserController@show
  POST /users/{u}/ban      Admin\UserController@ban
  DELETE /users/{u}/ban    Admin\UserController@unban
  POST /users/{u}/warn     Admin\UserController@warn
  GET  /categories         Admin\CategoryController@index
  POST /categories         Admin\CategoryController@store
  PATCH /categories/{c}    Admin\CategoryController@update
  DELETE /categories/{c}   Admin\CategoryController@destroy
  GET  /moderators         Admin\ModeratorController@index
  POST /moderators         Admin\ModeratorController@assign
  DELETE /moderators/{u}   Admin\ModeratorController@remove
  GET  /statistics         Admin\StatisticsController@index
  POST /statistics/snapshot Admin\StatisticsController@snapshot
  GET  /analytics          Admin\StatisticsController@index
  GET  /badges             StaticController@badges
```

---

## Static Pages

Pages with no backend data rendered as Blade views extending `layouts.app`:
- notifications, messages, groups, groupShow, badges — all behind `auth`
- forgot-password — behind `guest`

Handled by a single `StaticController`.

---

## What Gets Removed

- `laravel/sanctum` package
- `routes/api.php` (emptied or left with comment)
- `create_personal_access_tokens_table` migration (drop or skip)
- All `JsonResponse` return types and `response()->json()` calls
- `auth:sanctum` middleware references

---

## Success Criteria

- `GET /` shows paginated feed from DB using Sociala template
- Login/register work with session cookies, banned users blocked
- Profile show/edit work
- Admin pages render with real data using template layouts
- Static placeholder pages render without errors
- No JSON responses anywhere in web routes
- All existing models/migrations unchanged
