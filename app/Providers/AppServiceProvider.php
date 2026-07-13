<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Scopes\TenantScope;
use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Builder;
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
        Builder::macro('withoutTenancy', function () {
            return $this->withoutGlobalScope(TenantScope::class);
        });

        Product::observe(ProductObserver::class);

        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $rolePrefix = match (true) {
                    $user->hasRole('Super Admin')  => 'admin',
                    $user->hasRole('Manager')      => 'manager',
                    $user->hasRole('Gudang')       => 'gudang',
                    $user->hasRole('Tenant Owner') => 'admin',
                    default                        => 'app',
                };
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
