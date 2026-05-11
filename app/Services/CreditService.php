<?php

namespace App\Services;

use App\Enums\CreditTransactionType;
use App\Models\CreditTransaction;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreditService
{
    /**
     * Manually adjust a customer's credit balance.
     * Positive amount = add credit, negative = deduct.
     */
    public function adjustManually(Customer $customer, float $amount, string $note, User $adjustedBy, ?string $paymentMethod = null, ?string $referenceNo = null): CreditTransaction
    {
        return DB::transaction(function () use ($customer, $amount, $note, $adjustedBy, $paymentMethod, $referenceNo) {
            $newBalance = (float) $customer->credit_balance + $amount;

            $customer->update(['credit_balance' => max(0, $newBalance)]);

            return CreditTransaction::create([
                'customer_id'    => $customer->id,
                'type'           => $amount >= 0 ? 'manual_credit' : 'manual_debit',
                'payment_method' => $paymentMethod,
                'reference_no'   => $referenceNo,
                'amount'         => $amount,
                'balance_after'  => max(0, $newBalance),
                'note'           => $note,
                'created_by'     => $adjustedBy->id,
            ]);
        });
    }

    /**
     * Apply credit to a booking payment.
     */
    public function applyToBooking(Customer $customer, float $amount, int $bookingId): CreditTransaction
    {
        return DB::transaction(function () use ($customer, $amount, $bookingId) {
            if ((float) $customer->credit_balance < $amount) {
                throw new \RuntimeException('Insufficient credit balance.');
            }

            $newBalance = (float) $customer->credit_balance - $amount;
            $customer->update(['credit_balance' => $newBalance]);

            return CreditTransaction::create([
                'customer_id'   => $customer->id,
                'booking_id'    => $bookingId,
                'type'          => 'booking_payment',
                'amount'        => -$amount,
                'balance_after' => $newBalance,
                'note'          => "Applied to booking #{$bookingId}",
            ]);
        });
    }

    /**
     * Refund credit back to customer on cancellation.
     */
    public function refund(Customer $customer, float $amount, int $bookingId): CreditTransaction
    {
        return DB::transaction(function () use ($customer, $amount, $bookingId) {
            $newBalance = (float) $customer->credit_balance + $amount;
            $customer->update(['credit_balance' => $newBalance]);

            return CreditTransaction::create([
                'customer_id'   => $customer->id,
                'booking_id'    => $bookingId,
                'type'          => 'refund',
                'amount'        => $amount,
                'balance_after' => $newBalance,
                'note'          => "Refund from booking #{$bookingId}",
            ]);
        });
    }
}
