<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A Razorpay order for one cover page. It is marked paid when the payment is captured, whether the browser confirms
 * it first or Razorpay's webhook does; marking it twice changes nothing.
 */
#[Fillable(['student_id', 'razorpay_order_id', 'razorpay_payment_id', 'amount', 'currency', 'status', 'paid_at'])]
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Mark the order paid. Safe to call more than once (the browser and the webhook both do): an order that is
     * already paid is left as it is.
     */
    public function markPaid(string $razorpayPaymentId): void
    {
        static::whereKey($this->getKey())
            ->where('status', '!=', PaymentStatus::Paid)
            ->update(['status' => PaymentStatus::Paid, 'razorpay_payment_id' => $razorpayPaymentId, 'paid_at' => now()]);

        $this->refresh();
    }

    /**
     * Record a failed attempt, unless the order has been paid since.
     */
    public function markFailed(string $razorpayPaymentId): void
    {
        static::whereKey($this->getKey())
            ->where('status', '!=', PaymentStatus::Paid)
            ->update(['status' => PaymentStatus::Failed, 'razorpay_payment_id' => $razorpayPaymentId]);

        $this->refresh();
    }

    /**
     * The amount in rupees, e.g. "₹10" or "₹12.50".
     */
    public function formattedAmount(): string
    {
        return self::formatPaise($this->amount);
    }

    public static function formatPaise(int $paise): string
    {
        return '₹'.($paise % 100 === 0 ? number_format($paise / 100) : number_format($paise / 100, 2));
    }
}
