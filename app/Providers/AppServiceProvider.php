<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\HTTP\Middleware\Subscribed;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Laravel\Cashier\Cashier;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('subscribed', function ($app) {
            return new Subscribed();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cashier::useCustomerModel(User::class);
        Paginator::useBootstrap();
    }
}
