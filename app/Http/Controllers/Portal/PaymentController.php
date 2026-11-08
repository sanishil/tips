<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCoverPriceRequest;
use App\Models\Payment;
use App\Models\Setting;
use App\Support\Razorpay;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Payments, in the admin portal only (/admin/payments): the price of a cover page and the Razorpay orders students
 * have made, newest first.
 */
class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        return view('portal.payments', [
            'portal' => $request->user()->role->value,
            'price' => Setting::coverPrice(),
            'isRazorpayConfigured' => Razorpay::isConfigured(),
            'payments' => Payment::with('student')->latest()->limit(500)->get(),
        ]);
    }

    public function updatePrice(UpdateCoverPriceRequest $request): RedirectResponse
    {
        $price = $request->priceInPaise();

        Setting::put(Setting::COVER_PRICE, (string) $price);

        $message = $price === 0 ? 'Printing is now free.' : 'Each cover page now costs '.Payment::formatPaise($price).'.';

        return redirect()->route('admin.payments')->with('status', $message);
    }
}
