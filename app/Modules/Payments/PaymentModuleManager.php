<?php

namespace App\Modules\Payments;

use App\Modules\Payments\Contracts\PaymentModule;
use InvalidArgumentException;

/** Registry of payment system modules (each payment system = a module). */
class PaymentModuleManager
{
    /** @var array<string, class-string<PaymentModule>> */
    private array $drivers;

    /** @param array<string, class-string<PaymentModule>> $drivers */
    public function __construct(array $drivers = [])
    {
        $this->drivers = $drivers ?: (array) config('payments.drivers', []);
    }

    /** @return array<string, string> code => human name */
    public function all(): array
    {
        $result = [];

        foreach ($this->drivers as $code => $class) {
            if (! class_exists($class)) {
                continue;
            }

            try {
                $result[$code] = app($class)->name();
            } catch (\Throwable) {
                $result[$code] = $code;
            }
        }

        return $result;
    }

    public function has(string $code): bool
    {
        return isset($this->drivers[$code]) && class_exists($this->drivers[$code]);
    }

    public function make(string $code): PaymentModule
    {
        if (! $this->has($code)) {
            throw new InvalidArgumentException("Unknown payment module [{$code}].");
        }

        return app($this->drivers[$code]);
    }

    /** Resolve the module for a provider model row. */
    public function forProvider(\App\Models\PaymentProvider $provider): PaymentModule
    {
        return $this->make($provider->driver);
    }
}
