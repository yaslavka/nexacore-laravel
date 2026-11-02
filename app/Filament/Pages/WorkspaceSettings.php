<?php

namespace App\Filament\Pages;

use App\Models\AuditEvent;
use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class WorkspaceSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.workspace-settings';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.settings.nav');
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public function getTitle(): string
    {
        return __('admin.pages.settings.title');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::current();
        $this->form->fill([
            'company_name' => $settings->company_name,
            'tagline' => $settings->tagline,
            'email' => $settings->email,
            'phone' => $settings->phone,
            'address' => $settings->address,
            'legal_entity' => $settings->legal_entity,
            'privacy_text' => (string) $settings->privacy_text,
            'terms_text' => (string) $settings->terms_text,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin.pages.settings.company'))->schema([
                    TextInput::make('company_name')->required()->maxLength(200)->label(__('admin.fields.company_name')),
                    TextInput::make('legal_entity')->maxLength(1000)->label(__('admin.fields.legal_entity')),
                    TextInput::make('tagline')->maxLength(500)->label(__('admin.fields.tagline')),
                    TextInput::make('email')->email()->maxLength(254)->label(__('admin.fields.email')),
                    TextInput::make('phone')->maxLength(100)->label(__('admin.fields.phone')),
                    TextInput::make('address')->maxLength(1000)->label(__('admin.fields.address')),
                ])->columns(2),
                Section::make(__('admin.pages.settings.legal'))
                    ->schema([
                        Textarea::make('privacy_text')->rows(6)->maxLength(12000)->label(__('admin.pages.settings.privacy'))->helperText(__('admin.pages.settings.privacy_hint')),
                        Textarea::make('terms_text')->rows(6)->maxLength(12000)->label(__('admin.pages.settings.terms'))->helperText(__('admin.pages.settings.terms_hint')),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        if (! (auth()->user()?->canManageSettings() ?? false)) {
            Notification::make()->title(__('admin.pages.settings.no_perms'))->danger()->send();

            return;
        }

        $data = $this->form->getState();
        Setting::saveCurrent($data);
        AuditEvent::record('settings.updated', '', auth()->user()?->email ?? 'system');
        Notification::make()->title(__('admin.pages.settings.saved'))->success()->send();
    }
}
