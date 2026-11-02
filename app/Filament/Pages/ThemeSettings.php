<?php

namespace App\Filament\Pages;

use App\Models\AuditEvent;
use App\Support\Themes\ThemeManager;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ThemeSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static string $view = 'filament.pages.theme-settings';

    public static ?int $navigationSort = 0;

    /**
     * Currently selected (radio) theme slug. The accordion on the page is
     * driven by this property: the selected theme is expanded, the rest
     * are collapsed to just their names.
     */
    public string $activeTheme = '';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.themes.nav');
    }

    public function getTitle(): string
    {
        return __('admin.pages.themes.title');
    }

    public function mount(): void
    {
        $this->activeTheme = app(ThemeManager::class)->activeSlug();
    }

    /** Discovered themes for the accordion (slug => Theme). */
    public function getThemesProperty(): array
    {
        return app(ThemeManager::class)->all();
    }

    /** Slug of the theme that is really live on the public site. */
    public function getLiveThemeProperty(): string
    {
        return app(ThemeManager::class)->activeSlug();
    }

    /** Base directory where themes are discovered from. */
    public function getThemesPathProperty(): string
    {
        return app(ThemeManager::class)->basePath();
    }

    public function save(): void
    {
        $manager = app(ThemeManager::class);

        try {
            $theme = $manager->setActive($this->activeTheme);
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('admin.pages.themes.switch_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        // Make sure the newly activated theme's assets are exposed.
        $manager->publishAssets();

        AuditEvent::record('theme.activated', $theme->slug(), auth()->user()?->email ?? 'system');

        Notification::make()
            ->title(__('admin.pages.themes.activated'))
            ->body(__('admin.pages.themes.activated_body', ['theme' => $theme->name()]))
            ->success()
            ->send();
    }
}
