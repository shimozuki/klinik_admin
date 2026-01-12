<?php

namespace App\Providers;

use App\Models\Reservasi;
use Illuminate\Support\ServiceProvider;
use App\Observers\ReservasiObserver;

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
        Reservasi::observe(ReservasiObserver::class);
    }
}
