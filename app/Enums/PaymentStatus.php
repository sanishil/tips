<?php

namespace App\Enums;

/**
 * A Razorpay order starts as created, becomes paid once the payment is captured, or failed if the attempt didn't go
 * through. A failed order can still become paid: Razorpay lets the student retry within the same order.
 */
enum PaymentStatus: string
{
    case Created = 'created';
    case Paid = 'paid';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Not paid',
            self::Paid => 'Paid',
            self::Failed => 'Failed',
        };
    }

    /**
     * The Bootstrap colour of this status's badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Created => 'secondary',
            self::Paid => 'success',
            self::Failed => 'danger',
        };
    }
}
