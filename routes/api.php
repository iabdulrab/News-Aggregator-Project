<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SourceController;
use App\Http\Controllers\Api\UserPreferenceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes - Authentication
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Public routes - Articles (read-only)
Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/{id}', [ArticleController::class, 'show']);
    Route::get('/meta/categories', [ArticleController::class, 'categories']);
    Route::get('/meta/authors', [ArticleController::class, 'authors']);
});

// Public routes - Sources (read-only)
Route::prefix('sources')->group(function () {
    Route::get('/', [SourceController::class, 'index']);
    Route::get('/{id}', [SourceController::class, 'show']);
});

// Protected routes - Require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // User preferences
    Route::prefix('preferences')->group(function () {
        Route::get('/', [UserPreferenceController::class, 'show']);
        Route::put('/', [UserPreferenceController::class, 'update']);
        Route::delete('/', [UserPreferenceController::class, 'destroy']);
    });

    // Personalized articles feed
    Route::get('/articles/personalized/feed', [ArticleController::class, 'personalized']);
});
