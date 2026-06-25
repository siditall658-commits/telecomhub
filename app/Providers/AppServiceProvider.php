<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Incident;
use App\Observers\IncidentObserver;

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
        // On dit à Laravel d'écouter les événements du modèle Incident
        Incident::observe(IncidentObserver::class);
    }
}