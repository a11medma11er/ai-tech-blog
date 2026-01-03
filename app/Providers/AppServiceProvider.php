<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Services\Interfaces\TrendSearchServiceInterface::class,
            \App\Services\GeminiTrendSearchService::class
        );

        $this->app->bind(
            \App\Services\Interfaces\ContentGeneratorInterface::class,
            \App\Services\GeminiContentGenerator::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
