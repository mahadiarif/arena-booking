<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Events\PaymentReceived;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Record a new payment against a booking.
     */
    public function recordPayment(Booking $booking, array $data, User $receivedBy): Payment
    {
        return DB::transaction(function () use ($booking, $data, $receivedBy) {
            $payment = Payment::create([
                'booking_id'   => $booking->id,
                'amount'       => $data['amount'],
                'method'       => $data['method'],
                'reference_no' => $data['reference_no'] ?? null,
                'received_by'  => $receivedBy->id,
                'paid_at'      => now(),
            ]);

            // Update booking paid_amount
            $newPaid = (float) $booking->paid_amount + (float) $data['amount'];
            $booking->update(['paid_amount' => $newPaid]);

            // Auto-complete if fully paid and checked in
            if ($newPaid >= $booking->total_amount && $booking->status === BookingStatus::CheckedIn) {
                $booking->update(['status' => BookingStatus::Completed]);
            }

            event(new PaymentReceived($payment));

            return $payment;
        });
    }

    /**
     * Get payment summary for a booking.
     */
    public function getPaymentSummary(Booking $booking): array
    {
        $payments = $booking->payments;

        return [
            'total_amount'  => (float) $booking->total_amount,
            'paid_amount'   => (float) $booking->paid_amount,
            'due_amount'    => max(0, (float) $booking->total_amount - (float) $booking->paid_amount),
            'payment_count' => $payments->count(),
            'last_payment'  => $payments->sortByDesc('paid_at')->first(),
            'methods_used'  => $payments->pluck('method')->unique()->values(),
        ];
    }

    /**
     * Generate invoice PDF for a booking.
     * Requires: composer require barryvdh/laravel-dompdf
     */
    public function generateInvoicePDF(Booking $booking): Response
    {
        $booking->load(['customer', 'venue', 'slot', 'payments.receivedBy']);

        // Requires barryvdh/laravel-dompdf
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('booking'));
            return $pdf->download("invoice-{$booking->booking_ref}.pdf");
        }

        // Fallback: return HTML invoice as response
        return response(view('pdf.invoice', compact('booking'))->render(), 200, [
            'Content-Type' => 'text/html',
        ]);
    }
}
