<?php

namespace App\Filament\Pages;

use App\Models\AuditEvent;
use App\Support\Themes\Theme;
use App\Support\Themes\ThemeManager;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class ThemeSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static string $view = 'filament.pages.theme-settings';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.themes.nav');
    }

    public static function getNavigationSort(): ?int
    {
        return 0;
    }

    public function getTitle(): string
    {
        return __('admin.pages.themes.title');
    }

    public function mount(): void
    {
        $this->form->fill([
            'active_theme' => app(ThemeManager::class)->activeSlug(),
        ]);
    }

    public function form(Form $form): Form
    {
        $manager = app(ThemeManager::class);
        $themes = $manager->all();

        return $form
            ->schema([
                Section::make(__('admin.pages.themes.installed'))
                    ->description(new HtmlString(__('admin.pages.themes.description')))
                    ->schema([
                        Radio::make('active_theme')
                            ->label(__('admin.pages.themes.active'))
                            ->options(collect($themes)->mapWithKeys(
                                fn (Theme $theme, string $slug) => [
                                    $slug => $theme->name().'  ·  v'.$theme->version(),
                                ]
                            )->all())
                            ->descriptions(collect($themes)->mapWithKeys(
                                fn (Theme $theme, string $slug) => [
                                    $slug => $theme->description()
                                        .'  —  '.implode(' / ', array_map(strtoupper(...), $theme->locales())),
                                ]
                            )->all())
                            ->columns(1)
                            ->required()
                            ->live(),
                    ]),

                Section::make(__('admin.pages.themes.how'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('themes_path')
                            ->label(__('admin.pages.themes.path'))
                            ->content(new HtmlString('<code>'.$manager->basePath().'</code>')),
                        \Filament\Forms\Components\Placeholder::make('active_path')
                            ->label(__('admin.pages.themes.resolved'))
                            ->content(new HtmlString('<code>'.$manager->activeSlug().'</code> — themes/{slug}/views + themes/{slug}/public')),
                        \Filament\Forms\Components\Placeholder::make('add_theme')
                            ->label(__('admin.pages.themes.add'))
                            ->content(new HtmlString(__('admin.pages.themes.add_hint'))),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $manager = app(ThemeManager::class);

        try {
            $theme = $manager->setActive((string) $data['active_theme']);
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
