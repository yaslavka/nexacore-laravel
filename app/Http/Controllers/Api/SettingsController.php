<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $settings = Setting::current();

        return response()->json([
            'companyName' => $settings->company_name,
            'tagline' => (string) $settings->tagline,
            'email' => $settings->email,
            'phone' => $settings->phone,
            'address' => $settings->address,
            'legalEntity' => $settings->legal_entity,
            'locale' => 'en',
            'privacyText' => (string) $settings->privacy_text,
            'termsText' => (string) $settings->terms_text,
        ]);
    }
}
