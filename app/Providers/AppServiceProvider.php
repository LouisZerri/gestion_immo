<?php

namespace App\Providers;

use App\Models\EtatDesLieux;
use Illuminate\Support\Facades\Route;
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
        Route::bind('etatDesLieux', function ($value) {
            return EtatDesLieux::findOrFail($value);
        });
    }
}
