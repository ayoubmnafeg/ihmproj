<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FilterRuleController;
use App\Http\Controllers\Admin\ModeratorController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// ── Public ──────────────────────────────────────────────────────────────────
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login',    [AuthController::class, 'login']);
Route::get('/publications',           [PublicationController::class, 'index']);
Route::get('/publications/{publication}',    [PublicationController::class, 'show']);

// ── Authenticated ────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // Profile
    Route::get('/profile',  [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);

    // Publications
    Route::post('/publications',          [PublicationController::class, 'store']);
    Route::patch('/publications/{publication}',  [PublicationController::class, 'update']);
    Route::delete('/publications/{publication}', [PublicationController::class, 'destroy']);

    // Comments (nested under publication)
    Route::get('/publications/{publication}/comments',  [CommentController::class, 'index']);
    Route::post('/publications/{publication}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}',                [CommentController::class, 'destroy']);

    // Reactions (on any content)
    Route::post('/contents/{content}/reactions', [ReactionController::class, 'toggle']);

    // Reports
    Route::post('/contents/{content}/reports', [ReportController::class, 'store']);

    // ── Moderator ─────────────────────────────────────────────────────────────
    Route::middleware('role.mod')->group(function () {
        Route::get('/reports',            [ReportController::class, 'index']);
        Route::patch('/reports/{report}', [ReportController::class, 'update']);
    });

    // ── Admin ─────────────────────────────────────────────────────────────────
    Route::middleware('role.admin')->prefix('admin')->group(function () {

        // Users
        Route::get('/users',                  [UserController::class, 'index']);
        Route::get('/users/{user}',           [UserController::class, 'show']);
        Route::post('/users/{user}/ban',      [UserController::class, 'ban']);
        Route::delete('/users/{user}/ban',    [UserController::class, 'unban']);
        Route::post('/users/{user}/warn',     [UserController::class, 'warn']);

        // Categories
        Route::get('/categories',               [CategoryController::class, 'index']);
        Route::post('/categories',              [CategoryController::class, 'store']);
        Route::patch('/categories/{category}',  [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // Filter rules
        Route::get('/filter-rules',                  [FilterRuleController::class, 'index']);
        Route::post('/filter-rules',                 [FilterRuleController::class, 'store']);
        Route::patch('/filter-rules/{filterRule}',   [FilterRuleController::class, 'update']);
        Route::delete('/filter-rules/{filterRule}',  [FilterRuleController::class, 'destroy']);

        // Statistics
        Route::get('/statistics',          [StatisticsController::class, 'index']);
        Route::post('/statistics/snapshot', [StatisticsController::class, 'snapshot']);

        // Moderators
        Route::get('/moderators',               [ModeratorController::class, 'index']);
        Route::post('/moderators',              [ModeratorController::class, 'assign']);
        Route::delete('/moderators/{user}',     [ModeratorController::class, 'remove']);
    });
});
