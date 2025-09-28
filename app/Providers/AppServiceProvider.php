<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale Carbon ke Bahasa Indonesia
        setlocale(LC_TIME, 'id_ID.UTF-8'); 
        Carbon::setLocale('id');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
