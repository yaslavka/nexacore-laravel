<?php

namespace App\Providers\Filament;

use App\Http\Middleware\SetAdminLocale;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook as RenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('NexaCore Workspace')
            ->favicon(theme_asset('assets/favicon.svg', 'nexacore'))
            ->colors([
                'primary' => Color::Yellow,
                'info' => Color::Zinc,
                'warning' => Color::Amber,
            ])
            // The admin is always dark: forced dark mode removes the
            // light/dark switcher and applies the dark class server-side.
            ->darkMode(true, isForced: true)
            ->defaultThemeMode(ThemeMode::Dark)
            // The single admin stylesheet (public/admin/css/index.css).
            // Loaded AFTER Filament's own styles so the overrides win the
            // cascade; never add <style> blocks in views — Filament drops them.
            ->renderHook(RenderHook::STYLES_AFTER, fn (): HtmlString => new HtmlString(
                '<link rel="stylesheet" href="'.asset('admin/css/index.css').'">'
            ))
            // Group names match resource::$navigationGroup; labels are
            // translated at render time for the active admin locale.
            ->navigationGroups([
                NavigationGroup::make(fn (): string => __('admin.groups.orders')),
                NavigationGroup::make(fn (): string => __('admin.groups.catalog')),
                NavigationGroup::make(fn (): string => __('admin.groups.payments')),
                NavigationGroup::make(fn (): string => __('admin.groups.system')),
            ])
            ->navigationItems([
                NavigationItem::make()
                    ->label(fn (): string => __('admin.nav.view_site'))
                    ->url('/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->group(fn (): string => __('admin.groups.system'))
                    ->sort(100),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \Filament\Pages\Dashboard::class,
                \App\Filament\Pages\WorkspaceSettings::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\WorkspaceStats::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetAdminLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
