<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditEvent;
use App\Models\Order;
use App\Models\Service;
use App\Services\CatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class OrderController extends Controller
{
    public function store(Request $request, CatalogService $catalog): JsonResponse
    {
        $ip = $request->ip() ?? 'unknown';
        if (RateLimiter::tooManyAttempts('orders:'.$ip, 20)) {
            return response()->json(['error' => 'Too many requests. Please try again later.'], 429);
        }
        RateLimiter::hit('orders:'.$ip, 3600);

        $data = json_decode((string) $request->getContent(), true);
        if (! is_array($data) || array_is_list($data)) {
            return response()->json(['error' => 'Invalid JSON'], 400);
        }

        $serviceId = (string) ($data['serviceId'] ?? '');
        if ($serviceId === '' || mb_strlen($serviceId) > 160) {
            return response()->json(['error' => 'Invalid field: service'], 400);
        }

        if ($serviceId === 'consultation') {
            $service = ['id' => 'consultation', 'title' => 'Project consultation', 'packages' => [['name' => 'Custom']]];
        } else {
            $model = $catalog->findService($serviceId);
            if (! $model) {
                return response()->json(['error' => 'Service not found'], 400);
            }
            $service = ['id' => $model->id, 'title' => $model->title, 'packages' => $model->packages ?? []];
        }

        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '' || mb_strlen($name) > 120) {
            return response()->json(['error' => 'Invalid field: name'], 400);
        }

        $email = trim((string) ($data['email'] ?? ''));
        if ($email === '' || mb_strlen($email) > 254 || ! preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
            return response()->json(['error' => 'Invalid email address'], 400);
        }

        if (($data['consent'] ?? null) !== true) {
            return response()->json(['error' => 'Consent to data processing is required'], 400);
        }

        $packageName = null;
        if (array_key_exists('packageName', $data) && $data['packageName'] !== null && $data['packageName'] !== '') {
            $packageName = trim((string) $data['packageName']);
            if (mb_strlen($packageName) > 160 || ! collect($service['packages'] ?? [])->contains(fn ($item) => ($item['name'] ?? null) === $packageName)) {
                return response()->json(['error' => 'Package not found'], 400);
            }
        }

        $company = isset($data['company']) && $data['company'] !== null ? trim((string) $data['company']) : '';
        if (mb_strlen($company) > 200) {
            return response()->json(['error' => 'Invalid field: company'], 400);
        }

        $message = isset($data['message']) && $data['message'] !== null ? trim((string) $data['message']) : '';
        if (mb_strlen($message) > 5000) {
            return response()->json(['error' => 'Invalid field: message'], 400);
        }

        $order = Order::query()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'reference' => sprintf('NX-%s-%s', strtoupper(base_convert((string) time(), 10, 36)), strtoupper(bin2hex(random_bytes(2)))),
            'service_id' => $service['id'],
            'service_title' => $service['title'],
            'package_name' => $packageName ?: null,
            'name' => $name,
            'email' => $email,
            'company' => $company !== '' ? $company : null,
            'message' => $message !== '' ? $message : null,
            'consent' => true,
            'consent_at' => now(),
            'status' => 'new',
            'payment_status' => 'not_connected',
            'notification_status' => 'not_connected',
        ]);

        AuditEvent::record('order.created', $order->id);

        return response()->json(['order' => $this->orderArray($order)], 201);
    }

    private function orderArray(Order $order): array
    {
        return [
            'id' => $order->id,
            'reference' => $order->reference,
            'serviceId' => $order->service_id,
            'serviceTitle' => $order->service_title,
            'packageName' => $order->package_name,
            'name' => $order->name,
            'email' => $order->email,
            'company' => $order->company,
            'message' => $order->message,
            'consent' => (bool) $order->consent,
            'consentAt' => $order->consent_at?->toISOString(),
            'createdAt' => $order->created_at?->toISOString(),
            'status' => $order->status,
            'paymentStatus' => $order->payment_status,
            'notificationStatus' => $order->notification_status,
        ];
    }
}
