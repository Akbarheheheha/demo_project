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

    // ─────────────────────────────────────────────
    // Logout
    // ─────────────────────────────────────────────
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ─────────────────────────────────────────────
    // Root — role-based redirect
    // ─────────────────────────────────────────────
    Route::get('/', function () {
        $user = auth()->user();
        if ($user->hasRole('Kasir'))      return redirect()->route('pos');
        if ($user->hasRole('Gudang'))     return redirect()->route('gudang.inventory');
        if ($user->hasRole('Super Admin')) return redirect()->route('admin.dashboard');
        if ($user->hasRole('Manager'))    return redirect()->route('manager.dashboard');
        return redirect()->route('login');
    })->name('home');

    // ─────────────────────────────────────────────
    // Profile
    // ─────────────────────────────────────────────
    Route::get('/profile', fn () => view('profile'))->name('profile');

    // ─────────────────────────────────────────────
    // POS — Kasir & Super Admin
    // ─────────────────────────────────────────────
    Route::middleware(['role:Kasir|Super Admin'])->group(function () {
        Route::get('/pos', [PosController::class, 'launcher'])->name('pos');
        Route::get('/pos/fullscreen', [PosController::class, 'index'])->name('pos.fullscreen');
        Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
        Route::get('/pos/receipt/{transaction}', [PosController::class, 'receipt'])->name('pos.receipt');
    });

    // ─────────────────────────────────────────────
    // Super Admin — /admin/*
    // ─────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware(['role:Super Admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');
        Route::resource('cashiers', CashierController::class)->names('cashiers');
        Route::resource('categories', CategoryController::class)->names('categories');
        Route::resource('expenses', ExpenseController::class)->names('expenses');
        Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
        Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
        Route::put('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');
        Route::patch('/payment-methods/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggleActive'])->name('payment-methods.toggle');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    });

    // ─────────────────────────────────────────────
    // Manager — /manager/*
    // ─────────────────────────────────────────────
    Route::prefix('manager')->name('manager.')->middleware(['role:Manager'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');
        Route::resource('cashiers', CashierController::class)->names('cashiers');
        Route::resource('categories', CategoryController::class)->names('categories');
        Route::resource('expenses', ExpenseController::class)->names('expenses');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
        Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
        Route::put('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');
        Route::patch('/payment-methods/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggleActive'])->name('payment-methods.toggle');
    });


    // ─────────────────────────────────────────────
    // Dashboard API — Super Admin & Manager
    // ─────────────────────────────────────────────
    Route::middleware(['role:Super Admin|Manager'])->group(function () {
        Route::get('/admin/api/dashboard/low-stock', [DashboardController::class, 'getLowStockApi'])->name('dashboard.low-stock');
        Route::get('/admin/api/dashboard/sales-trend', [DashboardController::class, 'getSalesTrendApi'])->name('dashboard.sales-trend');
    });

    // ─────────────────────────────────────────────
    // Gudang — /gudang/*
    // ─────────────────────────────────────────────
    Route::prefix('gudang')->name('gudang.')->middleware(['role:Gudang'])->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    });

    // ─────────────────────────────────────────────
    // Shared API Routes
    // ─────────────────────────────────────────────
    Route::middleware(['role:Super Admin|Manager|Gudang'])->group(function () {
        Route::post('/api/inventory/store', [InventoryController::class, 'store']);
        Route::put('/api/inventory/update/{id}', [InventoryController::class, 'update']);
        Route::delete('/api/inventory/delete/{id}', [InventoryController::class, 'destroy']);
    });

    Route::middleware(['role:Super Admin|Manager|Gudang'])->group(function () {
        Route::post('/api/categories/store', [CategoryController::class, 'storeApi']);
        Route::put('/api/categories/update/{id}', [CategoryController::class, 'updateApi']);
        Route::delete('/api/categories/delete/{id}', [CategoryController::class, 'destroyApi']);
    });

    Route::middleware(['role:Super Admin|Manager'])->group(function () {
        Route::post('/api/expenses', [ExpenseController::class, 'storeApi']);
        Route::delete('/api/expenses/{expense}', [ExpenseController::class, 'destroyApi']);
    });

    Route::middleware(['role:Super Admin'])->group(function () {
        Route::post('/api/settings/save/{category}', [SettingsController::class, 'save']);
        Route::post('/api/settings/users/store', [SettingsController::class, 'storeUser']);
        Route::delete('/api/settings/users/delete/{id}', [SettingsController::class, 'deleteUser']);
        Route::post('/api/settings/payment-methods', [PaymentMethodController::class, 'storeApi']);
        Route::patch('/api/settings/payment-methods/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggleActiveApi']);
        Route::delete('/api/settings/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroyApi']);
    });

    // ─────────────────────────────────────────────
    // Notification Routes
    // ─────────────────────────────────────────────
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});
