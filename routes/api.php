<?php

use App\Presentation\Http\Controllers\AuthenticationController;
use App\Presentation\Http\Controllers\DashboardController;
use App\Presentation\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthenticationController::class, 'login'])->name('login');

Route::prefix('songs')
    ->name('songs.')
    ->group(function () {
        Route::get('/', [HomeController::class, 'listSongs'])
            ->name('list')
            ->middleware('throttle:60,1');
        Route::post('suggest', [HomeController::class, 'suggestSong'])
            ->name('suggest')
            ->middleware('throttle:1,1');
    });

Route::middleware('auth:sanctum')
    ->prefix('dashboard/songs')
    ->name('dashboard.songs.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'listSongs'])->name('list');
        Route::get('/{id}', [DashboardController::class, 'showSong'])->name('show');
        Route::post('/', [DashboardController::class, 'addSong'])->name('add');
        Route::patch('/{id}', [DashboardController::class, 'updateSong'])->name('update');
        Route::delete('/{id}', [DashboardController::class, 'deleteSong'])->name('delete');

        Route::patch('/{id}/approve', [DashboardController::class, 'approveSong'])->name('approve');
        Route::patch('/{id}/reject', [DashboardController::class, 'rejectSong'])->name('reject');
    });
