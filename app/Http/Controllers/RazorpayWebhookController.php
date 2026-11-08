<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Support\Razorpay;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Razorpay's webhook (Dashboard → Webhooks, URL /razorpay/webhook, events payment.captured, order.paid and
 * payment.failed). It records the outcome even if the student closed the page before Checkout returned.
 *
 * Razorpay retries anything that isn't a 2xx, so events for unknown orders are acknowledged and ignored.
 */
class RazorpayWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        if (! Razorpay::isValidWebhookSignature($request->getContent(), (string) $request->header('X-Razorpay-Signature'))) {
            return response('Invalid signature.', 400);
        }

        $paymentEntity = $request->json('payload.payment.entity');
        $payment = filled($paymentEntity['order_id'] ?? null)
            ? Payment::where('razorpay_order_id', $paymentEntity['order_id'])->first()
            : null;

        if ($payment) {
            match ($request->json('event')) {
                'payment.captured', 'order.paid' => $payment->markPaid($paymentEntity['id']),
                'payment.failed' => $payment->markFailed($paymentEntity['id']),
                default => null,
            };
        }

        return response()->noContent();
    }
}
