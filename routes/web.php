<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CategoryController;

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

    // Profile Route
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    // POS Routes (Accessible by Cashier and Super Admin)
    Route::middleware(['role:Kasir|Super Admin'])->group(function () {
        Route::get('/pos', [PosController::class, 'launcher'])->name('pos');
        Route::get('/pos/fullscreen', [PosController::class, 'index'])->name('pos.fullscreen');
        Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
        Route::get('/pos/receipt/{transaction}', [PosController::class, 'receipt'])->name('pos.receipt');
    });

    // Admin Routes (Accessible by Super Admin and Manager)
    Route::prefix('admin')->middleware(['role:Super Admin|Manager'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        // Define 'dashboard' alias to maintain compatibility
        Route::get('/main-dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/dashboard/low-stock', [DashboardController::class, 'getLowStockApi'])->name('dashboard.low-stock');
        Route::get('/api/dashboard/sales-trend', [DashboardController::class, 'getSalesTrendApi'])->name('dashboard.sales-trend');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs');
        Route::resource('cashiers', CashierController::class);
        Route::resource('categories', CategoryController::class);
    });

    // Inventory API CRUD (Accessible by Super Admin and Manager)
    Route::middleware(['role:Super Admin|Manager'])->group(function () {
        Route::post('/api/inventory/store', [InventoryController::class, 'store']);
        Route::put('/api/inventory/update/{id}', [InventoryController::class, 'update']);
        Route::delete('/api/inventory/delete/{id}', [InventoryController::class, 'destroy']);

        // Category API CRUD
        Route::post('/api/categories/store', [CategoryController::class, 'storeApi']);
        Route::put('/api/categories/update/{id}', [CategoryController::class, 'updateApi']);
        Route::delete('/api/categories/delete/{id}', [CategoryController::class, 'destroyApi']);
    });

    // Settings & User Access Routes (Super Admin Only)
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::get('/admin/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/api/settings/save/{category}', [SettingsController::class, 'save']);
        Route::post('/api/settings/users/store', [SettingsController::class, 'storeUser']);
        Route::delete('/api/settings/users/delete/{id}', [SettingsController::class, 'deleteUser']);
    });

    // Notification Routes
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});
