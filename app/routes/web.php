<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ModeratorController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendRequestController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaticController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [StaticController::class, 'forgot'])->name('forgot-password');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [PublicationController::class, 'index'])->name('feed.index');
    Route::get('/publications/{publication}', [PublicationController::class, 'show'])->name('publications.show');
    Route::post('/publications', [PublicationController::class, 'store'])->name('publications.store');
    Route::delete('/publications/{publication}', [PublicationController::class, 'destroy'])->name('publications.destroy');

    Route::post('/publications/{publication}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::post('/contents/{content}/reactions', [ReactionController::class, 'toggle'])->name('reactions.toggle');
    Route::post('/contents/{content}/reports', [ReportController::class, 'store'])->name('reports.store');

    Route::get('/members', [FriendRequestController::class, 'index'])->name('members.index');
    Route::post('/friend-requests/{user}', [FriendRequestController::class, 'store'])->name('friend-requests.store');
    Route::patch('/friend-requests/{friendRequest}', [FriendRequestController::class, 'update'])->name('friend-requests.update');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/settings', [StaticController::class, 'settings'])->name('settings.index');
    Route::get('/notifications', [StaticController::class, 'notifications'])->name('notifications.index');
    Route::get('/messages', [StaticController::class, 'messages'])->name('messages.index');
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::post('/groups/{category}/follow', [GroupController::class, 'follow'])->name('groups.follow');
    Route::get('/groups/{id}', [StaticController::class, 'groupShow'])->name('groups.show');

    Route::middleware('role.mod')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::patch('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    });

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
