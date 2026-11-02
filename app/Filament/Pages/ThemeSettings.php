<?php

namespace App\Filament\Pages;

use App\Support\Themes\Theme;
use App\Support\Themes\ThemeManager;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ThemeSettings extends Page
{
    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static string $view = 'filament.pages.theme-settings';

    protected static ?string $navigationLabel = 'Themes';

    protected static ?string $title = 'Themes';

    public ?array $data = [];

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
                Section::make('Installed themes')
                    ->description(new HtmlString(
                        'Pick the theme used for the public website. Every theme ships its own '
                        .'Blade views and assets, so layouts may differ completely. '
                        .'Drop a new folder with a <code>theme.json</code> into <code>themes/</code> to add one.'
                    ))
                    ->schema([
                        Radio::make('active_theme')
                            ->label('Active theme')
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

                Section::make('How it works')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Placeholder::make('themes_path')
                            ->label('Themes directory')
                            ->content(new HtmlString('<code>'.$manager->basePath().'</code>')),
                        Placeholder::make('active_path')
                            ->label('Resolved active theme')
                            ->content(new HtmlString('<code>'.$manager->activeSlug().'</code> — themes/{slug}/views + themes/{slug}/public')),
                        Placeholder::make('add_theme')
                            ->label('Adding a theme')
                            ->content(new HtmlString(
                                'Copy <code>themes/starter</code>, set a unique <code>slug</code> in <code>theme.json</code>, '
                                .'adjust <code>views/layout.blade.php</code> and assets, run <code>php artisan themes:list</code> — '
                                .'then select it here.'
                            )),
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
                ->title('Theme switch failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        // Make sure the newly activated theme's assets are exposed.
        $manager->publishAssets();

        Notification::make()
            ->title('Theme activated')
            ->body($theme->name().' is now used by the public website.')
            ->success()
            ->send();
    }
}
