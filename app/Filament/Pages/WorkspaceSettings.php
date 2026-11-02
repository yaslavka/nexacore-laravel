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
    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.workspace-settings';

    protected static ?string $navigationLabel = 'Settings';

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
                Section::make('Company')->schema([
                    TextInput::make('company_name')->required()->maxLength(200),
                    TextInput::make('legal_entity')->maxLength(1000),
                    TextInput::make('tagline')->maxLength(500),
                    TextInput::make('email')->email()->maxLength(254),
                    TextInput::make('phone')->maxLength(100),
                    TextInput::make('address')->maxLength(1000),
                ])->columns(2),
                Section::make('Legal texts')
                    ->schema([
                        Textarea::make('privacy_text')->rows(6)->maxLength(12000)->helperText('Optional custom privacy text.'),
                        Textarea::make('terms_text')->rows(6)->maxLength(12000)->helperText('Optional custom terms text.'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        if (! (auth()->user()?->canManageSettings() ?? false)) {
            Notification::make()->title('Insufficient permissions')->danger()->send();

            return;
        }

        $data = $this->form->getState();
        Setting::saveCurrent($data);
        AuditEvent::record('settings.updated', '', auth()->user()?->email ?? 'system');
        Notification::make()->title('Settings saved')->success()->send();
    }
}
