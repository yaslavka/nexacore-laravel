<?php

namespace App\Providers;

use App\Support\Themes\ThemeManager;
use Illuminate\Support\ServiceProvider;
use Throwable;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeManager::class);
    }

    public function boot(): void
    {
        $manager = $this->app->make(ThemeManager::class);

        // Blade namespaces: themes/{slug}/views → view('{slug}::*').
        $manager->registerViewNamespaces();

        // Expose themes/{slug}/public at /themes/{slug}/… for HTTP requests.
        // CLI (artisan, queue workers) publishes via `themes:publish` instead.
        if ((bool) config('themes.auto_publish', true) && PHP_SAPI !== 'cli') {
            try {
                $manager->publishAssets();
            } catch (Throwable $e) {
                logger()->warning('Theme asset publishing failed: '.$e->getMessage());
            }
        }
    }
}
