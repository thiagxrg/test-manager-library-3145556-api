<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Library\Bootstrap\Composition\PortBindings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        PortBindings::register($this->app);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
