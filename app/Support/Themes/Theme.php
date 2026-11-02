<?php

namespace App\Support\Themes;

use Illuminate\Contracts\Support\Arrayable;

/**
 * A single installable theme. Every theme lives in `{base}/themes/{directory}`
 * and must ship a `theme.json` manifest:
 *
 * ```json
 * {
 *     "slug": "nexacore",
 *     "name": "NexaCore Classic",
 *     "version": "1.0.0",
 *     "description": "…",
 *     "author": "…",
 *     "locales": ["en", "ru", "ky"]
 * }
 * ```
 *
 * Directory layout of a theme:
 *
 * ```
 * themes/nexacore/
 * ├── theme.json          # manifest
 * ├── views/              # Blade views, namespaced as `{slug}::*`
 * └── public/             # assets exposed at /themes/{slug}/…
 *     ├── css/
 *     ├── js/
 *     └── assets/
 * ```
 */
class Theme implements Arrayable
{
    public function __construct(
        public readonly string $slug,
        public readonly array $manifest,
        public readonly string $path,
    ) {}

    /** Build a theme from a directory, or null when it has no valid manifest. */
    public static function fromDirectory(string $directory): ?self
    {
        $manifestPath = $directory.DIRECTORY_SEPARATOR.'theme.json';

        if (! is_file($manifestPath)) {
            return null;
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true);

        if (! is_array($manifest) || empty($manifest['slug'])) {
            return null;
        }

        $slug = (string) $manifest['slug'];

        if (! preg_match('/^[a-z0-9][a-z0-9_-]*$/i', $slug)) {
            return null;
        }

        return new self(
            slug: strtolower($slug),
            manifest: $manifest,
            path: $directory,
        );
    }

    public function name(): string
    {
        return (string) ($this->manifest['name'] ?? ucfirst($this->slug));
    }

    public function version(): string
    {
        return (string) ($this->manifest['version'] ?? '1.0.0');
    }

    public function description(): string
    {
        return (string) ($this->manifest['description'] ?? '');
    }

    public function author(): string
    {
        return (string) ($this->manifest['author'] ?? '');
    }

    /** Locales the theme claims to support (informational). */
    public function locales(): array
    {
        $locales = $this->manifest['locales'] ?? ['en'];

        return is_array($locales) ? array_values($locales) : ['en'];
    }

    public function hasLocale(string $locale): bool
    {
        return in_array($locale, $this->locales(), true);
    }

    /** Directory with the theme's Blade views. */
    public function viewsPath(): string
    {
        return $this->path.DIRECTORY_SEPARATOR.'views';
    }

    public function hasViews(): bool
    {
        return is_dir($this->viewsPath());
    }

    /** Directory with the theme's public assets (css / js / fonts / images). */
    public function publicPath(): string
    {
        return $this->path.DIRECTORY_SEPARATOR.'public';
    }

    public function hasPublicAssets(): bool
    {
        return is_dir($this->publicPath());
    }

    /** Public URL for an asset inside the theme's public folder. */
    public function assetUrl(string $path = ''): string
    {
        return '/themes/'.$this->slug.'/'.ltrim($path, '/');
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name(),
            'version' => $this->version(),
            'description' => $this->description(),
            'author' => $this->author(),
            'locales' => $this->locales(),
            'path' => $this->path,
            'asset_url' => $this->assetUrl(),
        ];
    }
}
