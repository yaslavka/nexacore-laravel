<?php

use App\Support\Themes\Theme;
use App\Support\Themes\ThemeManager;
use Illuminate\Contracts\View\View as ViewContract;

if (! function_exists('theme_manager')) {
    /** Get the theme manager instance. */
    function theme_manager(): ThemeManager
    {
        return app(ThemeManager::class);
    }
}

if (! function_exists('theme')) {
    /** Get the currently active theme. */
    function theme(): Theme
    {
        return theme_manager()->active();
    }
}

if (! function_exists('theme_view')) {
    /**
     * Render a view from the active theme's Blade namespace, e.g.
     * `theme_view('layout', [...])` → `view('nexacore::layout', [...])`.
     *
     * If the active theme does not provide the requested view, the first
     * theme that does is used as a graceful fallback.
     */
    function theme_view(string $view, array $data = [], ?string $slug = null): ViewContract
    {
        $manager = theme_manager();
        $namespace = $slug ?? $manager->activeSlug();

        if (! view()->exists($namespace.'::'.$view)) {
            foreach (array_keys($manager->all()) as $candidate) {
                if (view()->exists($candidate.'::'.$view)) {
                    $namespace = $candidate;
                    break;
                }
            }
        }

        return view($namespace.'::'.$view, $data);
    }
}

if (! function_exists('theme_asset')) {
    /**
     * URL to an asset shipped by the active (or explicitly given) theme,
     * e.g. `theme_asset('css/styles.css')` → `/themes/nexacore/css/styles.css`.
     */
    function theme_asset(string $path = '', ?string $slug = null): string
    {
        $theme = $slug !== null ? theme_manager()->find($slug) : theme();

        return $theme?->assetUrl($path) ?? '/'.ltrim($path, '/');
    }
}
