<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Demo Gateway — secure payment</title>
<style>
    :root { color-scheme: dark; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        min-height: 100vh; display: flex; align-items: center; justify-content: center;
        background: radial-gradient(1200px 600px at 50% -10%, #26221a 0%, #121212 55%, #0a0a0a 100%);
        color: #fafaf9; font: 15px/1.55 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; padding: 24px;
    }
    .card {
        width: 100%; max-width: 420px; background: #1c1c1a; border: 1px solid #2e2b22;
        border-top: 3px solid #eab308; border-radius: 18px; padding: 28px; box-shadow: 0 30px 60px -30px rgb(234 179 8 / .25);
    }
    .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; font-size: 12px; color: #eab308; }
    .brand small { color: #a8a29e; letter-spacing: normal; text-transform: none; font-weight: 400; }
    h1 { font-size: 20px; margin: 18px 0 4px; }
    .product { color: #a8a29e; font-size: 14px; margin-bottom: 18px; }
    .total { display: flex; justify-content: space-between; align-items: baseline; background: #23211a; border: 1px solid #33301f; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px; }
    .total span { color: #a8a29e; font-size: 13px; }
    .total strong { font-size: 22px; color: #facc15; }
    label { display: block; font-size: 12px; color: #a8a29e; margin: 12px 0 4px; }
    input {
        width: 100%; background: #141412; color: #fafaf9; border: 1px solid #33301f; border-radius: 10px;
        padding: 11px 12px; font-size: 15px; outline: none;
    }
    input:focus { border-color: #eab308; box-shadow: 0 0 0 2px rgb(234 179 8 / .25); }
    .row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    button {
        width: 100%; margin-top: 20px; background: #eab308; color: #1c1917; border: 0; border-radius: 10px;
        padding: 13px; font-weight: 700; font-size: 15px; cursor: pointer; transition: .15s;
    }
    button:hover { background: #facc15; }
    .note { margin-top: 14px; font-size: 12px; color: #78716c; text-align: center; }
    .test-badge { display: inline-block; margin-left: auto; font-size: 11px; background: #3f3d33; color: #fde68a; padding: 3px 8px; border-radius: 999px; }
    @media (max-width: 480px) { .card { padding: 22px; } }
</style>
</head>
<body>
    <main class="card">
        <div class="brand">Demo Gateway <span class="test-badge">TEST ENVIRONMENT</span></div>
        <h1>Secure payment</h1>
        <p class="product">{{ $payment->payload['product'] ?? 'NexaCore services' }}</p>
        <div class="total">
            <span>Amount to pay</span>
            <strong>{{ $payment->currency }} {{ $amount }}</strong>
        </div>
        <form method="post" action="{{ url('/pay/demo/'.$payment->id.'/confirm') }}" autocomplete="off">
            @csrf
            <label for="card">Card number (any 16 digits)</label>
            <input id="card" name="card" inputmode="numeric" maxlength="16" placeholder="4242 4242 4242 4242" required>
            <div class="row">
                <div>
                    <label for="expiry">Expiry (MM/YY)</label>
                    <input id="expiry" name="expiry" placeholder="12/30" maxlength="5" required>
                </div>
                <div>
                    <label for="cvv">CVV</label>
                    <input id="cvv" name="cvv" inputmode="numeric" maxlength="3" placeholder="123" required>
                </div>
            </div>
            <button type="submit">Pay {{ $payment->currency }} {{ $amount }}</button>
        </form>
        <p class="note">This is a simulated payment page of the demo module. No real money moves.</p>
    </main>
</body>
</html>
