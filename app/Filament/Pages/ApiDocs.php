<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * In-admin reference of every public API endpoint of this server.
 * Endpoint definitions live here; all texts are translated (EN/RU/KY)
 * in the admin language files (admin.api.*).
 */
class ApiDocs extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    protected static string $view = 'filament.pages.api-docs';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.groups.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.api_docs.nav');
    }

    public function getTitle(): string
    {
        return __('admin.pages.api_docs.title');
    }

    public function getBaseUrlProperty(): string
    {
        return rtrim(url('/'), '/');
    }

    /**
     * Endpoint registry: id → method/path/auth. Descriptions, parameter
     * tables and notes are pulled from the admin language files so the
     * documentation follows the admin locale (EN/RU/KY).
     *
     * auth: null (public) | 'api_key' (X-Api-Key when configured)
     *      | 'internal' (called by a payment system, not by clients)
     */
    public function getEndpointsProperty(): array
    {
        return [
            ['id' => 'health', 'method' => 'GET', 'path' => '/api/health', 'auth' => null],
            ['id' => 'settings', 'method' => 'GET', 'path' => '/api/settings', 'auth' => null],
            ['id' => 'catalog', 'method' => 'GET', 'path' => '/api/catalog', 'auth' => null],
            ['id' => 'orders', 'method' => 'POST', 'path' => '/api/orders', 'auth' => null],
            ['id' => 'charge', 'method' => 'POST', 'path' => '/api/v1/payments/{provider}/charge', 'auth' => 'api_key'],
            ['id' => 'payment_show', 'method' => 'GET', 'path' => '/api/payments/{token}', 'auth' => null],
            ['id' => 'payment_start', 'method' => 'POST', 'path' => '/api/payments/{token}/start', 'auth' => null],
            ['id' => 'payment_status', 'method' => 'POST', 'path' => '/api/payments/{token}/status', 'auth' => null],
            ['id' => 'callback', 'method' => 'POST', 'path' => '/api/payments/{provider}/callback', 'auth' => 'internal'],
            ['id' => 'demo_complete', 'method' => 'POST', 'path' => '/api/payments/{token}/demo/complete', 'auth' => null],
        ];
    }

    /** JSON response examples per endpoint (locale-independent). */
    public function responseFor(string $id): ?string
    {
        return match ($id) {
            'health' => <<<'JSON'
{
    "status": "ok"
}
JSON,
            'settings' => <<<'JSON'
{
    "company": {
        "name": "NexaCore",
        "email": "hello@nexacore.kg",
        "phone": "+996 …"
    },
    "active_theme": "nexacore"
}
JSON,
            'catalog' => <<<'JSON'
{
    "categories": [
        { "id": "…", "name": "…" }
    ],
    "services": [
        {
            "id": "1-1",
            "title": "Corporate Websites",
            "price": 800,
            "packages": [ { "name": "…" } ]
        }
    ]
}
JSON,
            'orders' => <<<'JSON'
{
    "id": "9f1c…-uuid",
    "reference": "NX-LZ2K4-9A1F",
    "service": "Corporate Websites",
    "payment_status": "pending"
}
JSON,
            'charge' => <<<'JSON'
{
    "customer": {
        "id": "01a1…-uuid",
        "email": "client@example.com",
        "name": "Client",
        "created": true,
        "password": "ie9ZREL2tsMCo9"
    },
    "payment": {
        "id": "84b8…64-hex",
        "amount": 10000,
        "currency": "KGS",
        "status": "created",
        "expiresAt": "2026-11-02T17:35:22.000000Z"
    },
    "merchant": {
        "name": "Payler NexaCore",
        "theme": "nexacore",
        "provider": "payler"
    },
    "paymentUrl": "https://sandbox.payler.com/gapi/Pay?session_id=…"
}
JSON,
            'payment_show' => <<<'JSON'
{
    "payment": {
        "reference": "API-0ABBD9F1",
        "product": "Payler sandbox E2E test",
        "amount": 10000,
        "currency": "KGS",
        "mode": "sandbox",
        "status": "created",
        "expiresAt": "2026-11-02T17:35:22.000000Z",
        "paidAt": null,
        "expired": false
    }
}
JSON,
            'payment_start' => <<<'JSON'
{
    "paymentUrl": "https://sandbox.payler.com/gapi/Pay?session_id=…",
    "mode": "sandbox"
}
JSON,
            'payment_status' => <<<'JSON'
{
    "payment": {
        "reference": "API-0ABBD9F1",
        "status": "paid",
        "paidAt": "2026-11-02T16:36:45.000000Z"
    }
}
JSON,
            'callback' => <<<'JSON'
{
    "received": true,
    "status": "paid"
}
JSON,
            'demo_complete' => <<<'JSON'
{
    "status": "paid"
}
JSON,
            default => null,
        };
    }
}
