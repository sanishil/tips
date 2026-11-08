<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| One row per Razorpay order: a student pays for one cover page before it prints. Amounts are in paise.
*/
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('razorpay_order_id', 40)->unique();
            $table->string('razorpay_payment_id', 40)->nullable()->unique();
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('INR');
            $table->string('status', 20)->default('created');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
