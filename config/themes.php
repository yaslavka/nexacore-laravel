<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Themes root directory
    |--------------------------------------------------------------------------
    |
    | Every subdirectory of this path that contains a valid `theme.json`
    | manifest is discovered as an installable theme. Each theme bundles
    | its own Blade views (`views/`) and public assets (`public/`),
    | so different themes may ship completely different layouts.
    |
    */

    'path' => base_path('themes'),

    /*
    |--------------------------------------------------------------------------
    | Published assets directory
    |--------------------------------------------------------------------------
    |
    | Each theme's `public/` folder is exposed through
    | /themes/{slug}/... — either by symlink (fast, instant updates)
    | or by copying the files (Windows / shared hosting).
    |
    */

    'public_path' => public_path('themes'),

    /*
    |--------------------------------------------------------------------------
    | Publish strategy
    |--------------------------------------------------------------------------
    |
    | Supported: "symlink" (default) or "copy".
    |
    */

    'publish' => env('THEMES_PUBLISH', 'symlink'),

    /*
    |--------------------------------------------------------------------------
    | Auto publish on boot
    |--------------------------------------------------------------------------
    |
    | When enabled, missing /themes/{slug} links are created automatically
    | on the first HTTP request after a theme is installed.
    |
    */

    'auto_publish' => env('THEMES_AUTO_PUBLISH', true),

    /*
    |--------------------------------------------------------------------------
    | Fallback / default theme
    |--------------------------------------------------------------------------
    |
    | Used when no active theme has been persisted in the database settings
    | yet. Can be overridden per environment via THEME_ACTIVE.
    |
    */

    'active' => env('THEME_ACTIVE', 'nexacore'),

];
