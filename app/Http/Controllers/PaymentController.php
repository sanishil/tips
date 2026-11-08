<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmPaymentRequest;
use App\Models\Payment;
use App\Support\Razorpay;
use Illuminate\Http\JsonResponse;

/**
 * Razorpay Checkout's success handler posts here, so the cover page can be made straight away instead of waiting for
 * the webhook. The webhook still records the payment if the browser never gets this far.
 */
class PaymentController extends Controller
{
    public function confirm(ConfirmPaymentRequest $request): JsonResponse
    {
        $orderId = $request->validated('razorpay_order_id');
        $paymentId = $request->validated('razorpay_payment_id');

        if (! Razorpay::isValidPaymentSignature($orderId, $paymentId, $request->validated('razorpay_signature'))) {
            return response()->json(['message' => "We couldn't verify that payment. If money was taken, contact the office with the payment ID."], 422);
        }

        $payment = Payment::where('razorpay_order_id', $orderId)->firstOrFail();
        $payment->markPaid($paymentId);

        return response()->json(['paid' => true]);
    }
}
