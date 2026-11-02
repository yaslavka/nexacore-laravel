<?php

namespace App\Filament\Pages;

use App\Models\AuditEvent;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Validation\ValidationException;

class AdminLocale extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-language';

    protected static string $view = 'filament.pages.admin-locale';

    public ?string $locale = null;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.language.nav');
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public function getTitle(): string
    {
        return __('admin.pages.language.title');
    }

    public function mount(): void
    {
        $this->locale = admin_locale();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin.pages.language.title'))
                    ->description(__('admin.pages.language.description'))
                    ->schema([
                        Radio::make('locale')
                            ->label(__('admin.pages.language.current'))
                            ->options(
                                site_languages()
                                    ->mapWithKeys(fn ($language) => [
                                        $language->code => $language->native_name,
                                    ])
                                    ->all()
                            )
                            ->descriptions(
                                site_languages()
                                    ->mapWithKeys(fn ($language) => [
                                        $language->code => $language->name.' · '.$language->code,
                                    ])
                                    ->all()
                            )
                            ->required()
                            ->columns(1),
                    ]),
            ])
            ->statePath('');
    }

    public function save(): void
    {
        $this->validate();

        if (! in_array($this->locale, site_locales(), true)) {
            throw ValidationException::withMessages([
                'locale' => __('admin.pages.language.invalid'),
            ]);
        }

        $user = auth()->user();

        if ($user) {
            $user->locale = $this->locale;
            $user->save();
        }

        session(['admin_locale' => $this->locale]);

        AuditEvent::record('admin.locale.changed', $this->locale, $user?->email ?? 'system');

        Notification::make()
            ->title(__('admin.pages.language.applied'))
            ->body(__('admin.pages.language.applied_body', ['locale' => $this->locale]))
            ->success()
            ->send();

        $this->redirect(static::getUrl());
    }
}
