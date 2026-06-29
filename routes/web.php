<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AuthController;

// Auth Routes (Guest Only)
Route::middleware(['guest.custom'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected ERP Routes
Route::middleware(['auth.custom'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
