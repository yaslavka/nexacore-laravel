<?php

use App\Models\Language;
use Illuminate\Support\Collection;

if (! function_exists('site_languages')) {
    /** Active languages (Language models) available for the site and the admin. */
    function site_languages(): Collection
    {
        return Language::active();
    }
}

if (! function_exists('site_locales')) {
    /** Active locale codes, e.g. ['en', 'ru', 'ky']. */
    function site_locales(): array
    {
        return site_languages()->pluck('code')->all();
    }
}

if (! function_exists('default_site_locale')) {
    /** Default locale code (used as fallback for the public site). */
    function default_site_locale(): string
    {
        return Language::defaultCode();
    }
}

if (! function_exists('admin_locale')) {
    /**
     * Locale of the admin panel for the current request:
     * authenticated user preference → session → site default.
     */
    function admin_locale(): string
    {
        $locales = site_locales();

        $userLocale = auth()->user()?->locale;
        if ($userLocale && in_array($userLocale, $locales, true)) {
            return $userLocale;
        }

        $sessionLocale = session('admin_locale');
        if ($sessionLocale && in_array($sessionLocale, $locales, true)) {
            return $sessionLocale;
        }

        return default_site_locale();
    }
}
