<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveStudentRequest;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Student;
use App\Support\Razorpay;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Student records behind the cover page form: looked up by university roll number to fill the form in, and saved
 * when the student prints (updated if the roll number is already on file, created if not).
 *
 * Once the admin sets a price, saving also creates a Razorpay order; the page prints after the payment goes through
 * (see PaymentController and the webhook).
 */
class StudentController extends Controller
{
    public function show(string $tuRoll): JsonResponse
    {
        $student = Student::with('department')->where('tu_roll', $tuRoll)->firstOrFail();

        return response()->json($student->toCoverForm());
    }

    public function store(SaveStudentRequest $request): JsonResponse
    {
        $price = Setting::coverPrice();

        if ($price > 0 && ! Razorpay::isConfigured()) {
            return response()->json(['message' => "Payments aren't set up yet. Please try again later."], 503);
        }

        $student = Student::updateOrCreate(
            ['tu_roll' => $request->validated('tu_roll')],
            $request->studentAttributes(),
        );

        $status = $student->wasRecentlyCreated ? 201 : 200;

        if ($price === 0) {
            return response()->json(['saved' => true], $status);
        }

        try {
            $order = Razorpay::createOrder($price, 'cover-'.$student->id.'-'.now()->timestamp, ['tu_roll' => $student->tu_roll]);
        } catch (ConnectionException|RequestException $exception) {
            Log::error('Razorpay order could not be created.', ['error' => $exception->getMessage()]);

            return response()->json(['message' => "Couldn't start the payment. Please try again."], 503);
        }

        Payment::create([
            'student_id' => $student->id,
            'razorpay_order_id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
        ]);

        return response()->json([
            'saved' => true,
            'payment' => [
                'key' => Razorpay::keyId(),
                'order_id' => $order['id'],
                'amount' => $order['amount'],
                'currency' => $order['currency'],
                'name' => config('app.name'),
                'description' => 'Assignment cover page',
                'prefill' => (object) array_filter(['name' => $student->name, 'email' => $request->user()?->email]),
                'confirm_url' => route('payments.confirm'),
            ],
        ], $status);
    }
}
