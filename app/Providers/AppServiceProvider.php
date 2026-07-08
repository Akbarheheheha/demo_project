<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Product::observe(ProductObserver::class);

        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $rolePrefix = 'app';
                if ($user->hasRole('Super Admin')) {
                    $rolePrefix = 'admin';
                } elseif ($user->hasRole('Manager')) {
                    $rolePrefix = 'manager';
                } elseif ($user->hasRole('Gudang')) {
                    $rolePrefix = 'gudang';
                }
                $view->with([
                    'notifications' => $user->notifications()->take(10)->get(),
                    'unreadCount' => $user->unreadNotifications()->count(),
                    'rolePrefix' => $rolePrefix,
                ]);
            } else {
                $view->with([
                    'notifications' => collect(),
                    'unreadCount' => 0,
                    'rolePrefix' => 'app',
                ]);
            }
        });
    }
}
