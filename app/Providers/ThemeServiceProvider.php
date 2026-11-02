<?php

namespace App\Providers;

use App\Support\Themes\ThemeManager;
use Illuminate\Support\ServiceProvider;
use Throwable;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Helper files registered in composer.json "autoload.files". They are
     * re-required here defensively: on machines with a stale composer
     * autoloader (e.g. after `git pull` without `composer dump-autoload`)
     * the Filament panel would otherwise fail with
     * "Call to undefined function theme_asset()".
     */
    protected array $helperFiles = [
        'Support/Themes/helpers.php',
        'Support/Localization/helpers.php',
    ];

    public function register(): void
    {
        foreach ($this->helperFiles as $helper) {
            $path = app_path($helper);

            if (is_file($path)) {
                require_once $path;
            }
        }

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
