<?php

namespace App\Support;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * The parts of Razorpay the cover page uses: creating an order for Checkout, and checking the signatures Razorpay puts
 * on a completed Checkout and on its webhooks. Keys come from config/services.php (RAZORPAY_* in .env).
 */
class Razorpay
{
    private const ORDERS_URL = 'https://api.razorpay.com/v1/orders';

    /**
     * Whether the API keys are set, so orders can be created.
     */
    public static function isConfigured(): bool
    {
        return filled(config('services.razorpay.key_id')) && filled(config('services.razorpay.key_secret'));
    }

    public static function keyId(): ?string
    {
        return config('services.razorpay.key_id');
    }

    /**
     * Create an order for Checkout to collect.
     *
     * @param  array<string, string>  $notes
     * @return array{id: string, amount: int, currency: string}
     *
     * @throws RequestException
     */
    public static function createOrder(int $amountInPaise, string $receipt, array $notes = []): array
    {
        $order = Http::withBasicAuth((string) config('services.razorpay.key_id'), (string) config('services.razorpay.key_secret'))
            ->acceptJson()
            ->timeout(15)
            ->post(self::ORDERS_URL, [
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'receipt' => $receipt,
                'notes' => (object) $notes,
            ])
            ->throw()
            ->json();

        return [
            'id' => $order['id'],
            'amount' => (int) $order['amount'],
            'currency' => $order['currency'],
        ];
    }

    /**
     * Checkout's handler gets a signature: HMAC-SHA256 of "order_id|payment_id" with the key secret.
     */
    public static function isValidPaymentSignature(string $orderId, string $paymentId, string $signature): bool
    {
        $secret = (string) config('services.razorpay.key_secret');

        return $secret !== '' && hash_equals(hash_hmac('sha256', $orderId.'|'.$paymentId, $secret), $signature);
    }

    /**
     * Webhooks carry X-Razorpay-Signature: HMAC-SHA256 of the raw body with the webhook secret.
     */
    public static function isValidWebhookSignature(string $body, string $signature): bool
    {
        $secret = (string) config('services.razorpay.webhook_secret');

        return $secret !== '' && hash_equals(hash_hmac('sha256', $body, $secret), $signature);
    }
}
