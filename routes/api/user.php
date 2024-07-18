<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\TicketController;
use Illuminate\Support\Facades\Route;

Route::controller(TicketController::class)->prefix('ticket')->as('ticket.')->group(function () {
    Route::middleware(['auth:sanctum', 'abilities:user'])->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/show/{ticketId}', 'show')->name('show');
        Route::post('/store', 'store')->name('store.user'); 
        Route::get('/dashboardstats', 'dashboardStatistics')->name('dashboardStatistics');
    });
});

Route::controller(ProfileController::class)->prefix('profile')->group(function () {
    Route::middleware(['auth:sanctum', 'abilities:user'])->group(function () {
        Route::get('/show', 'show')->name('show');
        Route::put('/update', 'update')->name('update');
    });
});

Route::controller(AuthController::class)->group(function ()
{
    Route::middleware(['guest'])->group(function () {
        Route::post('/login', 'login')->name('login');
        Route::post('store', 'store')->name('store');
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', 'logout')->name('logout');
    });
});
