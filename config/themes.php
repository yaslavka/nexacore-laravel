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
    | /themes/{slug}/... as a REAL copy of the files.
    | Symlinks are never used: many Apache hosts (SymLinksIfOwnerMatch,
    | FollowSymLinks disabled) answer 403 for every asset behind them.
    |
    */

    'public_path' => public_path('themes'),

    /*
    |--------------------------------------------------------------------------
    | Auto publish on boot
    |--------------------------------------------------------------------------
    |
    | When enabled, missing /themes/{slug} copies are created automatically
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
