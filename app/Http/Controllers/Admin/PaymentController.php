<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
    ) {}

    public function store(StorePaymentRequest $request, Booking $booking): RedirectResponse
    {
        $payment = $this->paymentService->recordPayment(
            $booking,
            $request->validated(),
            auth()->user()
        );

        return back()->with(
            'success',
            'Payment of ৳' . number_format($payment->amount, 2) . ' recorded.'
        );
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        if (! auth()->user()->can('delete payments')) {
            abort(403);
        }

        if ($payment->booking->status === BookingStatus::Completed) {
            return back()->withErrors([
                'error' => 'Cannot delete a payment for a completed booking.',
            ]);
        }

        DB::transaction(function () use ($payment) {
            $booking = $payment->booking()->lockForUpdate()->first();
            $newPaid = max(0, (float) $booking->paid_amount - (float) $payment->amount);
            $booking->update(['paid_amount' => $newPaid]);
            $payment->delete();
        });

        return back()->with('success', 'Payment deleted.');
    }
}
