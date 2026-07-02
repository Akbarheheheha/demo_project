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
                $view->with([
                    'notifications' => $user->notifications()->take(10)->get(),
                    'unreadCount' => $user->unreadNotifications()->count(),
                ]);
            } else {
                $view->with([
                    'notifications' => collect(),
                    'unreadCount' => 0,
                ]);
            }
        });
    }
}
