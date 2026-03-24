<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HintController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

// ─── Publikus útvonalak ───────────────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ─── Authentikált útvonalak (Sanctum token + aktív fiók) ─────────────────────
Route::middleware(['auth:sanctum', 'is_active'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ─── Pálya útvonalak ─────────────────────────────────────────────────────
    Route::get('/levels',      [LevelController::class, 'index']);
    Route::get('/levels/{id}', [LevelController::class, 'show']);

    // ─── Kérdés útvonalak ────────────────────────────────────────────────────
    Route::get('/levels/{levelId}/questions',   [QuestionController::class, 'index']);
    Route::post('/questions/{id}/check-answer', [QuestionController::class, 'checkAnswer']);

    // ─── Hint útvonalak ──────────────────────────────────────────────────────
    Route::get('/questions/{questionId}/hints', [HintController::class, 'index']);
    Route::post('/hints/{id}/buy',              [HintController::class, 'buy']);

    // ─── Progress / kód beküldés ─────────────────────────────────────────────
    Route::post('/levels/{levelId}/submit-code', [ProgressController::class, 'submitCode']);

    // ─── Leaderboard ─────────────────────────────────────────────────────────
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);

    // ─── Admin-only útvonalak ─────────────────────────────────────────────────
    Route::middleware('is_admin')->prefix('admin')->group(function () {
        // Dashboard statisztikák
        Route::get('/stats', [AdminController::class, 'stats']);

        // Users CRUD
        Route::get('/users',          [AdminController::class, 'users']);
        Route::put('/users/{id}',     [AdminController::class, 'updateUser']);
        Route::delete('/users/{id}',  [AdminController::class, 'deleteUser']);

        // Levels CRUD
        Route::get('/levels',                  [AdminController::class, 'levels']);
        Route::post('/levels',                 [AdminController::class, 'createLevel']);
        Route::put('/levels/{id}',             [AdminController::class, 'updateLevel']);
        Route::delete('/levels/{id}',          [AdminController::class, 'deleteLevel']);

        // Questions CRUD
        Route::get('/questions',          [AdminController::class, 'questions']);
        Route::post('/questions',         [AdminController::class, 'createQuestion']);
        Route::put('/questions/{id}',     [AdminController::class, 'updateQuestion']);
        Route::delete('/questions/{id}',  [AdminController::class, 'deleteQuestion']);

        // Hints CRUD
        Route::get('/hints',          [AdminController::class, 'hints']);
        Route::post('/hints',         [AdminController::class, 'createHint']);
        Route::put('/hints/{id}',     [AdminController::class, 'updateHint']);
        Route::delete('/hints/{id}',  [AdminController::class, 'deleteHint']);
    });
});

// php artisan serve --port=8001 --host=0.0.0.0
