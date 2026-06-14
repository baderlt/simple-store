<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for MySQL index length issue
        Schema::defaultStringLength(191);
        
        // If you're using a different database or need specific settings
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        View::composer('admin.layouts.app', function ($view) {
            $newOrders = Order::query()
                ->where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get(['id', 'order_number', 'customer_name', 'total', 'created_at']);

            $view->with([
                'newOrders' => $newOrders,
                'newOrdersCount' => Order::where('status', 'pending')->count(),
            ]);
        });
    }
}
