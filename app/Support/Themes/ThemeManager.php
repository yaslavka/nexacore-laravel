<?php

namespace App\Support\Themes;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Discovers themes, resolves the active one and publishes theme assets.
 *
 * Resolution order for the active theme:
 *   1. `settings.active_theme` (persisted via the admin panel);
 *   2. `config('themes.active')` (env THEME_ACTIVE);
 *   3. the first discovered theme (alphabetical) as a last resort.
 */
class ThemeManager
{
    /** @var array<string, Theme>|null */
    protected ?array $discovered = null;

    protected ?Theme $active = null;

    /** @return array<string, Theme> all discovered themes keyed by slug. */
    public function all(): array
    {
        if ($this->discovered !== null) {
            return $this->discovered;
        }

        $themes = [];

        foreach (glob($this->basePath().'/*', GLOB_ONLYDIR) ?: [] as $directory) {
            $theme = Theme::fromDirectory($directory);

            if ($theme !== null) {
                $themes[$theme->slug] = $theme;
            }
        }

        ksort($themes);

        return $this->discovered = $themes;
    }

    public function find(string $slug): ?Theme
    {
        return $this->all()[$slug] ?? null;
    }

    public function has(string $slug): bool
    {
        return isset($this->all()[$slug]);
    }

    public function count(): int
    {
        return count($this->all());
    }

    public function basePath(): string
    {
        return (string) config('themes.path');
    }

    /** Currently active theme (DB setting → config → first discovered). */
    public function active(): Theme
    {
        if ($this->active instanceof Theme) {
            return $this->active;
        }

        $slug = null;

        try {
            $slug = Setting::query()->find(1)?->active_theme;
        } catch (Throwable) {
            // Database not migrated / unavailable yet — fall back to config.
        }

        $slug = filled($slug) ? (string) $slug : (string) config('themes.active');

        return $this->active = $this->find($slug)
            ?? $this->first()
            ?? throw new RuntimeException('No themes discovered in ['.$this->basePath().'].');
    }

    /** Slug of the active theme; never throws (config fallback). */
    public function activeSlug(): string
    {
        try {
            return $this->active()->slug;
        } catch (RuntimeException) {
            return (string) config('themes.active');
        }
    }

    /** Persist the active theme in the settings table. */
    public function setActive(string $slug): Theme
    {
        $theme = $this->find($slug) ?? throw new RuntimeException("Theme [{$slug}] is not installed.");

        Setting::saveCurrent(['active_theme' => $theme->slug]);

        return $this->active = $theme;
    }

    /** Register a Blade view namespace for every theme: view('{slug}::layout'). */
    public function registerViewNamespaces(): void
    {
        $finder = view()->getFinder();

        foreach ($this->all() as $theme) {
            if ($theme->hasViews()) {
                $finder->addNamespace($theme->slug, $theme->viewsPath());
            }
        }
    }

    /**
     * Expose every theme's `public/` folder at `public/themes/{slug}`.
     *
     * Published copies are kept in sync with the theme sources: on hosts
     * where symlinks are not available (e.g. Windows without developer
     * mode) the folder is a plain copy, so a `git pull` would otherwise
     * leave stale JS/CSS in the web root. A `.theme-published` signature
     * marker records the state of the source tree; when it no longer
     * matches, the copy is refreshed on the next request.
     *
     * @param  bool  $force  recreate existing links/copies
     * @return array<int, string> slugs that were (re)published now
     */
    public function publishAssets(bool $force = false): array
    {
        $published = [];

        foreach ($this->all() as $theme) {
            if (! $theme->hasPublicAssets()) {
                continue;
            }

            $target = rtrim((string) config('themes.public_path'), '/').DIRECTORY_SEPARATOR.$theme->slug;
            $source = $theme->publicPath();

            if ($force && file_exists($target)) {
                $this->deletePath($target);
            }

            // A dangling symlink would break file_exists()/mkdir() below.
            if (is_link($target) && ! file_exists($target)) {
                @unlink($target);
            }

            if (is_link($target)) {
                continue; // symlink target is always live
            }

            if (! file_exists($target)) {
                $this->linkOrCopy($source, $target);

                if (! is_link($target)) {
                    $this->storeSignature($target, $this->sourceSignature($source));
                }

                $published[] = $theme->slug;

                continue;
            }

            // Existing copy: refresh it when the source tree changed.
            $signature = $this->sourceSignature($source);

            if ($signature !== null && $this->storedSignature($target) !== $signature) {
                $this->deletePath($target);
                $this->copyDirectory($source, $target);
                $this->storeSignature($target, $signature);
                $published[] = $theme->slug;
            }
        }

        return $published;
    }

    /**
     * Cheap fingerprint of a theme's source asset tree (relative paths,
     * file sizes and modification times).
     */
    protected function sourceSignature(string $source): ?string
    {
        if (! is_dir($source)) {
            return null;
        }

        $parts = [];
        $this->collectSignature($source, '', $parts);

        return md5(implode('|', $parts));
    }

    protected function collectSignature(string $dir, string $prefix, array &$parts): void
    {
        foreach (scandir($dir) ?: [] as $item) {
            if (in_array($item, ['.', '..'], true)) {
                continue;
            }

            $path = $dir.DIRECTORY_SEPARATOR.$item;
            $relative = $prefix === '' ? $item : $prefix.'/'.$item;

            if (is_link($path)) {
                continue;
            }

            if (is_dir($path)) {
                $parts[] = $relative.'/';
                $this->collectSignature($path, $relative, $parts);

                continue;
            }

            $size = @filesize($path);
            $mtime = @filemtime($path);
            $parts[] = $relative.':'.($size === false ? '?' : $size).':'.($mtime === false ? '?' : $mtime);
        }
    }

    protected function storedSignature(string $target): ?string
    {
        $content = @file_get_contents($target.DIRECTORY_SEPARATOR.'.theme-published');

        return $content === false ? null : trim($content);
    }

    protected function storeSignature(string $target, ?string $signature): void
    {
        if ($signature === null) {
            return;
        }

        @file_put_contents($target.DIRECTORY_SEPARATOR.'.theme-published', $signature);
    }

    /** Forget cached discovery (call after installing/removing themes at runtime). */
    public function flush(): void
    {
        $this->discovered = null;
        $this->active = null;
    }

    protected function first(): ?Theme
    {
        foreach ($this->all() as $theme) {
            return $theme;
        }

        return null;
    }

    protected function linkOrCopy(string $source, string $target): void
    {
        $parent = dirname($target);

        if (! is_dir($parent)) {
            @mkdir($parent, 0775, true);
        }

        if ((string) config('themes.publish') === 'symlink') {
            try {
                if (@symlink($source, $target)) {
                    return;
                }
            } catch (Throwable $e) {
                Log::warning('Theme asset symlink failed, falling back to copy.', [
                    'target' => $target,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->copyDirectory($source, $target);
    }

    protected function copyDirectory(string $source, string $target): void
    {
        @mkdir($target, 0775, true);

        foreach (scandir($source) ?: [] as $item) {
            if (in_array($item, ['.', '..'], true)) {
                continue;
            }

            $from = $source.DIRECTORY_SEPARATOR.$item;
            $to = $target.DIRECTORY_SEPARATOR.$item;

            if (is_dir($from)) {
                $this->copyDirectory($from, $to);
            } else {
                @copy($from, $to);
            }
        }
    }

    protected function deletePath(string $target): void
    {
        if (is_link($target)) {
            @unlink($target);

            return;
        }

        if (is_dir($target)) {
            foreach (scandir($target) ?: [] as $item) {
                if (in_array($item, ['.', '..'], true)) {
                    continue;
                }

                $path = $target.DIRECTORY_SEPARATOR.$item;

                if (is_dir($path) && ! is_link($path)) {
                    $this->deletePath($path);
                } else {
                    @unlink($path);
                }
            }

            @rmdir($target);

            return;
        }

        @unlink($target);
    }
}
