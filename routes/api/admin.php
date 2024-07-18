<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\TicketController;
use Illuminate\Support\Facades\Route;

Route::controller(TicketController::class)->prefix('ticket')->as('ticket.')->group(function () {
    Route::middleware(['auth:sanctum', 'abilities:super_admin,admin,staff'])->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/show/{ticketId}', 'show')->name('show');
        Route::put('/update/{ticketId}', 'update')->name('update');
        Route::post('/store', 'store')->name('store');
        Route::get('/download/{ticketId}', 'download')->name('download');
        Route::get('/getTemporaryUrl/{ticketId}', 'getTemporaryUrl')->name('getTemporaryUrl');
        Route::get('/dashboardstats', 'dashboardStatistics')->name('dashboardStatistics');
    });
});

Route::controller(AdminController::class)->group(function () {
    Route::middleware(['auth:sanctum', 'abilities:super_admin'])->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::get('/show/{adminId}', 'show')->name('show');
        Route::put('/update/{adminId}', 'update')->name('update');
        Route::put('/updateStatus/{adminId}', 'updateStatus')->name('updateStatus');
    });
});

Route::controller(ClientController::class)->prefix('client')->group(function () {
    Route::middleware(['auth:sanctum', 'abilities:super_admin,admin'])->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/tickets/{userId}', 'tickets')->name('tickets');
        Route::post('/store', 'store')->name('store');
        Route::get('/show/{userId}', 'show')->name('show');
        Route::put('/update/{userId}', 'update')->name('update');
        Route::put('/updateStatus/{userId}', 'updateStatus')->name('updateStatus');
    });
});

Route::controller(ProfileController::class)->prefix('profile')->group(function () {
    Route::middleware(['auth:sanctum', 'abilities:super_admin,admin,staff'])->group(function () {
        Route::get('/show', 'show')->name('show');
        Route::put('/update', 'update')->name('update');
    });
});

Route::controller(AuthController::class)->group(function ()
{
    Route::middleware(['guest'])->group(function () {
        Route::post('/register', 'register')->name('register');
        Route::post('/login', 'login')->name('login');
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', 'logout')->name('logout');
    });
});