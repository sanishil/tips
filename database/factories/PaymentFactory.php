<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'razorpay_order_id' => 'order_'.fake()->unique()->bothify('##############'),
            'amount' => 1000,
            'currency' => 'INR',
            'status' => PaymentStatus::Created,
        ];
    }

    /**
     * A payment that has been captured.
     */
    public function paid(): static
    {
        return $this->state(fn (): array => [
            'status' => PaymentStatus::Paid,
            'razorpay_payment_id' => 'pay_'.fake()->unique()->bothify('##############'),
            'paid_at' => now(),
        ]);
    }
}
