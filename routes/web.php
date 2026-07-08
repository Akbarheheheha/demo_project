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
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentMethodController;

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
        if ($user->hasRole('Gudang')) {
            return redirect()->route('inventory');
        }
        if ($user->hasRole('Super Admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('manager.dashboard');
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

    // ─────────────────────────────────────────────
    // Super Admin Dashboard
    // ─────────────────────────────────────────────
    Route::prefix('admin')->middleware(['role:Super Admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/api/dashboard/low-stock', [DashboardController::class, 'getLowStockApi'])->name('dashboard.low-stock');
        Route::get('/api/dashboard/sales-trend', [DashboardController::class, 'getSalesTrendApi'])->name('dashboard.sales-trend');
    });

    // ─────────────────────────────────────────────
    // Manager Dashboard
    // ─────────────────────────────────────────────
    Route::prefix('manager')->middleware(['role:Manager'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('manager.dashboard');
    });

    // ─────────────────────────────────────────────
    // Shared Admin Routes (Super Admin & Manager)
    // ─────────────────────────────────────────────
    Route::prefix('admin')->middleware(['role:Super Admin|Manager'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs');
        Route::resource('cashiers', CashierController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('expenses', ExpenseController::class);
        Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
        Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
        Route::put('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');
        Route::patch('/payment-methods/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggleActive'])->name('payment-methods.toggle');
    });

    // ─────────────────────────────────────────────
    // Inventory (Super Admin, Manager & Gudang)
    // ─────────────────────────────────────────────
    Route::prefix('admin')->middleware(['role:Super Admin|Manager|Gudang'])->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    });

    Route::middleware(['role:Super Admin|Manager'])->group(function () {
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    });
    // Notification Routes
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});
