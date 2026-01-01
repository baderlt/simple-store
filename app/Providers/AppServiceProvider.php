<?php

namespace App\Providers;
use Illuminate\Support\Facades\Schema;
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
    }
}
