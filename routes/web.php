<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;

// Auth Routes (Guest Only)
Route::middleware(['guest.custom'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected ERP Routes
Route::middleware(['auth.custom'])->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Root route redirects based on user role
    Route::get('/', function () {
        $user = auth()->user();
        if ($user->hasRole('Kasir')) {
            return redirect()->route('pos');
        }
        return redirect()->route('admin.dashboard');
    })->name('home');

    // POS Routes (Accessible by Cashier and Super Admin)
    Route::middleware(['role:Kasir|Super Admin'])->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos');
        Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    });

    // Admin Routes (Accessible by Super Admin and Manager)
    Route::prefix('admin')->middleware(['role:Super Admin|Manager'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        // Define 'dashboard' alias to maintain compatibility
        Route::get('/main-dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    });

    // Inventory API CRUD (Accessible by Super Admin and Manager)
    Route::middleware(['role:Super Admin|Manager'])->group(function () {
        Route::post('/api/inventory/store', [InventoryController::class, 'store']);
        Route::put('/api/inventory/update/{id}', [InventoryController::class, 'update']);
        Route::delete('/api/inventory/delete/{id}', [InventoryController::class, 'destroy']);
    });

    // Settings & User Access Routes (Super Admin Only)
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::get('/admin/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/api/settings/save/{category}', [SettingsController::class, 'save']);
        Route::post('/api/settings/users/store', [SettingsController::class, 'storeUser']);
        Route::delete('/api/settings/users/delete/{id}', [SettingsController::class, 'deleteUser']);
    });
});
