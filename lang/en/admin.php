<?php

return [
    'groups' => [
        'orders' => 'Orders',
        'catalog' => 'Catalog',
        'payments' => 'Payments',
        'system' => 'System',
    ],

    'nav' => [
        'view_site' => 'View website',
    ],

    'actions' => [
        'open_on_site' => 'Open on website',
        'more' => 'More actions',
        'replicate' => 'Duplicate',
    ],

    'resources' => [
        'orders' => [
            'nav' => 'Enquiries',
            'singular' => 'Enquiry',
            'plural' => 'Enquiries',
        ],
        'payments' => [
            'nav' => 'Payments',
            'singular' => 'Payment',
            'plural' => 'Payments',
        ],
        'directions' => [
            'nav' => 'Directions',
            'singular' => 'Direction',
            'plural' => 'Directions',
        ],
        'services' => [
            'nav' => 'Services',
            'singular' => 'Service',
            'plural' => 'Services',
        ],
        'audit' => [
            'nav' => 'Audit log',
            'singular' => 'Audit event',
            'plural' => 'Audit log',
        ],
        'payment_providers' => [
            'nav' => 'Payment systems',
            'singular' => 'Payment system',
            'plural' => 'Payment systems',
        ],
        'payment_merchants' => [
            'nav' => 'Merchants',
            'singular' => 'Merchant',
            'plural' => 'Merchants',
        ],

        'languages' => [
            'nav' => 'Languages',
            'singular' => 'Language',
            'plural' => 'Languages',
        ],

        'companies' => [
            'nav' => 'Companies',
            'singular' => 'Company',
            'plural' => 'Companies',
        ],
    ],

    'pages' => [
        'dashboard' => 'Dashboard',

        'api_docs' => [
            'nav' => 'API documentation',
            'title' => 'API documentation',
        ],

        'themes' => [
            'nav' => 'Themes',
            'title' => 'Themes',
            'installed' => 'Installed themes',
            'description' => 'Pick the theme used for the public website. Every theme ships its own Blade views and assets, so layouts may differ completely. Drop a new folder with a <code>theme.json</code> into <code>themes/</code> to add one.',
            'active' => 'Active theme',
            'activate' => 'Activate theme',
            'slug' => 'Slug',
            'locales' => 'Site languages',
            'version' => 'Version',
            'author' => 'Author',
            'activated' => 'Theme activated',
            'activated_body' => ':theme is now used by the public website.',
            'switch_failed' => 'Theme switch failed',
            'how' => 'How it works',
            'path' => 'Themes directory',
            'resolved' => 'Resolved active theme',
            'add' => 'Adding a theme',
            'add_hint' => 'Copy <code>themes/starter</code>, set a unique <code>slug</code> in <code>theme.json</code>, adjust <code>views/layout.blade.php</code> and assets, run <code>php artisan themes:list</code> — then select it here.',
        ],

        'language' => [
            'nav' => 'Admin language',
            'title' => 'Admin language',
            'description' => 'Choose the language of the admin panel. The list is managed in “Languages”.',
            'current' => 'Language',
            'apply' => 'Apply language',
            'applied' => 'Language applied',
            'applied_body' => 'The admin panel now uses “:locale”.',
            'invalid' => 'This language is not active.',
        ],
    ],

    'stats' => [
        'new_enquiries' => 'New enquiries',
        'new_enquiries_desc' => 'Awaiting first response',
        'in_progress' => 'In progress',
        'completed' => 'Completed',
        'payments' => 'Payments',
        'payments_desc' => 'Active payment links',
    ],

    'fields' => [
        'reference' => 'Reference',
        'service' => 'Service',
        'package' => 'Package',
        'name' => 'Name',
        'email' => 'Email',
        'company' => 'Company',
        'message' => 'Message',
        'status' => 'Status',
        'payment' => 'Payment',
        'payment_status' => 'Payment status',
        'created_at' => 'Created',
        'token' => 'Token',
        'amount' => 'Amount (USD)',
        'amount_helper' => 'The amount is agreed with the client before invoicing.',
        'mode' => 'Mode',
        'number' => '#',
        'order_no' => 'Order No.',
        'id' => 'ID',
        'title' => 'Title',
        'description' => 'Description',
        'short' => 'Short',
        'short_title' => 'Short title',
        'services' => 'Services',
        'category' => 'Category',
        'price' => 'Price from (USD)',
        'price_helper' => 'Leave empty for a custom quote. Prices are indicative.',
        'duration' => 'Duration',
        'deliverables' => 'Deliverables',
        'packages' => 'Packages',
        'custom_quote' => 'Custom quote',
        'time' => 'Time',
        'actor' => 'Actor',
        'action' => 'Action',
        'target' => 'Target',
        'company_name' => 'Company name',
        'legal_entity' => 'Legal entity',
        'tagline' => 'Tagline',
        'phone' => 'Phone',
        'address' => 'Address',
    ],

    'tabs' => [
        'translations' => 'Translations',
        'english' => 'English (canonical)',
        'russian' => 'Russian',
        'kyrgyz' => 'Kyrgyz',
        'title_ru' => 'Title (ru)',
        'title_ky' => 'Title (ky)',
        'short_ru' => 'Short title (ru)',
        'short_ky' => 'Short title (ky)',
        'desc_ru' => 'Description (ru)',
        'desc_ky' => 'Description (ky)',
        'duration_en' => 'Duration (en)',
        'duration_ru' => 'Duration (ru)',
        'duration_ky' => 'Duration (ky)',
        'translated' => 'translated',
        'empty' => 'empty',
    ],

    'statuses' => [
        'new' => 'New',
        'in_progress' => 'In progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'pending' => 'Pending',
        'paid' => 'Paid',
        'expired' => 'Expired',
    ],

    'order_actions' => [
        'payment_link' => 'Payment link',
        'export_csv' => 'Export CSV',
        'backup_json' => 'Backup JSON',
    ],

    'languages' => [
        'code' => 'Code',
        'code_hint' => 'Lowercase locale code, e.g. en, ru, ky.',
        'name' => 'Name (English)',
        'native' => 'Native name',
        'active' => 'Active',
        'default' => 'Default',
        'default_hint' => 'The default language is used as fallback, cannot be deactivated or deleted.',
        'sort' => 'Sort order',
        'created' => 'Language created',
        'updated' => 'Language updated',
        'deleted' => 'Language deleted',
        'make_default' => 'Make default language',
        'default_changed' => 'Default language updated',
    
        'cannot_delete_default' => 'The default language cannot be deleted.',
    ],

    'companies' => [
        'section_general' => 'General',
        'section_legal' => 'Legal texts',
        'privacy' => 'Privacy text',
        'terms' => 'Terms text',
        'privacy_hint' => 'Optional custom privacy text shown on the public site.',
        'terms_hint' => 'Optional custom terms text shown on the public site.',
        'is_default' => 'Primary company',
        'is_default_hint' => 'The primary company is shown on the public website, in invoices and in GET /api/settings. There is always exactly one primary company.',
        'badge_primary' => 'Primary',
        'badge_secondary' => 'Additional',
        'make_primary' => 'Make primary',
        'make_primary_body' => '“:name” will be shown on the public website instead of the current primary company.',
        'made_primary' => 'Company is now primary',
        'made_primary_body' => '“:name” is now shown on the public website.',
        'last_company' => 'At least one company must remain',
        'created' => 'Company added',
        'updated' => 'Company updated',
        'deleted' => 'Company deleted',
    ],

    'payment_providers' => [
        'created' => 'Payment system added',
        'updated' => 'Payment system updated',
        'deleted' => 'Payment system deleted',
        'driver_hint' => 'Module class that implements the payment flow. New payment systems are added as modules without changing the core.',
    ],

    'payment_merchants' => [
        'singular' => 'Merchant',
        'created' => 'Merchant added',
        'updated' => 'Merchant updated',
        'deleted' => 'Merchant deleted',
        'unique_hint' => 'A merchant can be linked to each theme of the site.',
    ],

    'payment_fields' => [
        'provider' => 'Payment system',
        'driver' => 'Module (driver)',
        'theme' => 'Site theme',
        'theme_hint' => 'The merchant serves payments only for this theme. "*" — any theme.',
        'wildcard_any_theme' => 'Any theme',
        'host' => 'Gateway host',
        'host_hint' => 'For example, secure.payler.com or sandbox.payler.com.',
        'terminal_key' => 'Terminal key',
        'key_hint' => 'Issued by the payment system for the merchant account.',
        'terminal_secret' => 'Terminal secret',
        'secret_hint' => 'Stored encrypted.',
        'mode' => 'Mode',
        'mode_live' => 'Live',
        'mode_test' => 'Sandbox',
        'currency' => 'Currency',
        'operation_type' => 'Operation type',
        'one_step' => 'One-step payment',
        'two_step' => 'Two-step payment',
        'lifetime' => 'Session lifetime (minutes)',
        'is_active' => 'Active',
        'callback_url' => 'Callback URL',
        'callback_hint' => 'Set this URL in the payment system back office to receive payment notifications.',
        'session_id' => 'Session ID',
        'provider_order_id' => 'Order ID in payment system',
        'customer' => 'Customer',
        'payment_url' => 'Payment page URL',
        'provider_status' => 'Status in payment system',
    ],

    'api' => [
        'intro' => 'Base URL: <code>:url</code>. Every endpoint answers JSON (UTF-8). Bodies are JSON unless noted otherwise. <code>{provider}</code> — payment system code (e.g. <code>payler</code>), <code>{token}</code> — payment token (64 hex characters).',

        'rules_title' => 'General rules',
        'rules' => [
            'Request and response bodies are JSON (Content-Type: application/json). The only exception is a gateway callback: Payler sends it as x-www-form-urlencoded.',
            'Authentication: when PAYMENTS_API_TOKEN is configured, the charge endpoint requires the X-Api-Key header (a Bearer token or ?api_token= are also accepted). All other endpoints are public but rate limited.',
            'Amounts are integers in minimal monetary units (tiyin/cents): 10000 = 100.00 KGS.',
            'Payment merchants are bound to site themes: the theme parameter (or the active theme of the site) selects which merchant serves the payment; a merchant with the wildcard theme “*” serves any theme.',
            'Errors return {"error": "…"} with the proper HTTP code (400 / 401 / 404 / 410 / 422 / 429 / 502). Validation failures answer 422 with {"error", "errors"}.',
            'Every endpoint is rate limited — on HTTP 429 slow down and retry later.',
        ],

        'auth' => [
            'none' => 'Public',
            'api_key' => 'API key',
            'internal' => 'Payment system',
        ],

        'table' => [
            'name' => 'Parameter',
            'type' => 'Type',
            'required' => 'Required',
            'desc' => 'Description',
            'yes' => 'Yes',
            'no' => 'No',
        ],

        'no_params' => 'No request parameters.',
        'response_title' => 'Response example',

        'health' => [
            'title' => 'Service health check',
            'desc' => 'Returns the current status of the API. Used by monitoring and uptime checks.',
        ],

        'settings' => [
            'title' => 'Public settings',
            'desc' => 'Public website settings: company contacts, legal texts, active theme. No authentication.',
        ],

        'catalog' => [
            'title' => 'Catalog',
            'desc' => 'Published services and categories rendered on the public site, including packages and prices.',
        ],

        'orders' => [
            'title' => 'Create order (enquiry)',
            'desc' => 'Creates an enquiry/order from the public site form. Rate limited: 20 requests per hour per IP.',
            'params' => [
                ['serviceId', 'string', true, 'Service ID from the catalog or “consultation”.'],
                ['name', 'string ≤ 120', true, 'Client name.'],
                ['email', 'string, e-mail', true, 'Client e-mail.'],
                ['packageName', 'string', false, 'Package name — must exist among the service packages.'],
                ['company', 'string ≤ 200', false, 'Company name.'],
                ['message', 'string ≤ 5000', false, 'Free-form message.'],
                ['consent', 'boolean', true, 'Must be exactly true — consent to personal data processing.'],
            ],
            'notes' => 'HTTP 429 is returned when the rate limit is exceeded.',
        ],

        'charge' => [
            'title' => 'Payment charge — customer + payment session',
            'desc' => 'The main payment API. Creates the customer in the database (auto-generated password, delivered once in this response and by e-mail), creates the order and the payment, opens a session at the payment system and returns the URL of the hosted payment form. The merchant is selected by the site theme — every theme can have its own merchant. Rate limited: 30 requests per minute.',
            'params' => [
                ['{provider} (URL)', 'string', true, 'Payment system code, e.g. payler.'],
                ['email', 'string, e-mail', true, 'Customer e-mail; used to find or create the customer.'],
                ['name', 'string ≤ 120', false, 'Customer name.'],
                ['phone', 'string ≤ 40', false, 'Customer phone.'],
                ['amount', 'integer ≥ 1', true, 'Amount in minimal monetary units (tiyin/cents).'],
                ['currency', 'string, 3 letters', false, 'ISO 4217 code; default — the merchant currency (KGS).'],
                ['product', 'string ≤ 250', false, 'Payment description shown to the customer.'],
                ['theme', 'string', false, 'Site theme that selects the merchant; default — the active site theme.'],
                ['lang', 'en | ru', false, 'Language of the payment form; default — ru.'],
                ['return_url_success', 'URL', false, 'Where the customer returns after a successful payment.'],
                ['return_url_decline', 'URL', false, 'Where the customer returns after a failed payment.'],
                ['metadata', 'object', false, 'Any extra data stored together with the payment.'],
            ],
            'notes' => '“password” is present only when the customer is created for the first time (created: true) — store it immediately; it is never sent again. The payment link lives for the merchant-configured lifetime (default 60 minutes).',
        ],

        'payment_show' => [
            'title' => 'Payment info',
            'desc' => 'Current state of a payment by its token: amount, status, mode, expiry.',
            'params' => [
                ['{token} (URL)', '64 hex', true, 'Payment token (payment.id from the charge response).'],
            ],
        ],

        'payment_start' => [
            'title' => 'Start / restart the payment session',
            'desc' => 'Opens (or re-opens) the gateway session for the payment and returns the URL of the hosted payment form. Used by the checkout page before redirecting the customer.',
            'params' => [
                ['{token} (URL)', '64 hex', true, 'Payment token.'],
                ['consent', 'boolean', true, 'Must be exactly true — the customer accepted the offer and the refund policy.'],
            ],
            'notes' => 'HTTP 410 is returned when the payment link has expired.',
        ],

        'payment_status' => [
            'title' => 'Payment status (with gateway sync)',
            'desc' => 'Returns the payment state; if a gateway session exists and the payment is not final yet, the status is refreshed from the payment system first (GetStatus for Payler). Used for polling after the return from the gateway.',
            'params' => [
                ['{token} (URL)', '64 hex', true, 'Payment token.'],
            ],
            'notes' => 'Poll while the status is created / pending; paid, reversed, refunded and cancelled are final.',
        ],

        'callback' => [
            'title' => 'Gateway notification (callback)',
            'desc' => 'The endpoint a payment system calls on status changes — for Payler these are “guaranteed notifications” (POST, x-www-form-urlencoded, order_id). The notification carries no signature, so the status is always re-verified with the gateway (GetStatus) before it is applied locally. Configure this URL in the merchant back office of the payment system.',
            'params' => [
                ['{provider} (URL)', 'string', true, 'Payment system code, e.g. payler.'],
                ['order_id', 'string', true, 'Order ID in the payment system (our provider_order_id).'],
            ],
            'notes' => 'Answer 200 stops the gateway retries; 503 asks the gateway to try again later.',
        ],

        'demo_complete' => [
            'title' => 'Demo gateway completion',
            'desc' => 'Completes a payment session of the built-in demo module (no real money). Simulates what a real payment system callback does. Applicable to demo sessions only.',
            'params' => [
                ['{token} (URL)', '64 hex', true, 'Payment token of a demo session.'],
            ],
        ],
    ],
];
