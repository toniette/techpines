<?php

use App\Presentation\Http\Controllers\DashboardController;
use App\Presentation\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'songs', 'name' => 'songs.'], function () {
    Route::get('ranking', [HomeController::class, 'rankSongs']);
    Route::get('list', [HomeController::class, 'listSongs']);
    Route::post('suggest', [HomeController::class, 'suggestSong']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [DashboardController::class, 'listSongs']);
        Route::get('/{id}', [DashboardController::class, 'showSong']);
        Route::post('/', [DashboardController::class, 'createSong']);
        Route::patch('/{id}', [DashboardController::class, 'updateSong']);
        Route::delete('/{id}', [DashboardController::class, 'deleteSong']);
    });
});
