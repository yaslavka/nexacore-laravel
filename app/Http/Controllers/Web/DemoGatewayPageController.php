<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Modules\Payments\Drivers\DemoModule;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Simulated hosted payment page for the built-in demo module.
 * Stands in for the real PSP page ({host}/gpay/{session_id}) so the
 * complete checkout flow works without real merchant credentials.
 */
class DemoGatewayPageController extends Controller
{
    public function show(string $token): View
    {
        abort_unless(preg_match('/^[a-f0-9]{64}$/', $token) && ($payment = Payment::find($token)), 404);
        abort_unless(str_starts_with((string) $payment->session_id, DemoModule::PREFIX), 404);

        return view('payments.demo-gateway', [
            'payment' => $payment,
            'amount' => number_format($payment->amount / 100, 2, '.', ' '),
        ]);
    }

    public function confirm(Request $request, string $token, PaymentService $payments): RedirectResponse
    {
        abort_unless(preg_match('/^[a-f0-9]{64}$/', $token) && ($payment = Payment::find($token)), 404);
        abort_unless(str_starts_with((string) $payment->session_id, DemoModule::PREFIX), 404);

        $request->validate([
            'card' => ['required', 'digits:16'],
            'expiry' => ['required', 'regex:/^\d{2}\/\d{2}$/'],
            'cvv' => ['required', 'digits:3'],
        ]);

        if ($payment->status !== Payment::STATUS_PAID) {
            $payments->applyStatus($payment, 'paid');
        }

        return redirect()->to('/checkout/'.$payment->id.'?result=success');
    }
}
