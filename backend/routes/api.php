<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ReportController;
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

    // ─── Progress / kód beküldés ─────────────────────────────────────────────
    Route::post('/levels/{levelId}/submit-code', [ProgressController::class, 'submitCode']);

    // ─── Leaderboard ─────────────────────────────────────────────────────────
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);

    // ─── Bejelentések ────────────────────────────────────────────────────────
    Route::post('/reports', [ReportController::class, 'store']);

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

        // Questions CRUD (opciókkal)
        Route::get('/questions',          [AdminController::class, 'questions']);
        Route::post('/questions',         [AdminController::class, 'createQuestion']);
        Route::put('/questions/{id}',     [AdminController::class, 'updateQuestion']);
        Route::delete('/questions/{id}',  [AdminController::class, 'deleteQuestion']);

        // Reports
        Route::get('/reports',            [AdminController::class, 'reports']);
        Route::put('/reports/{id}',       [AdminController::class, 'updateReport']);
        Route::delete('/reports/{id}',    [AdminController::class, 'deleteReport']);
    });
});
