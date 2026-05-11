<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send booking confirmation to customer (SMS + optional email).
     */
    public function sendBookingConfirmation(Booking $booking): void
    {
        $customer = $booking->customer;
        if (! $customer) return;

        $message = $this->buildConfirmationMessage($booking);

        $this->sendSms($customer->phone, $message, $booking);
    }

    /**
     * Send 24-hour reminder to customer.
     */
    public function sendReminder(Booking $booking): void
    {
        $customer = $booking->customer;
        if (! $customer) return;

        $slotDate = $booking->slot?->date?->format('d M Y');
        $slotTime = $booking->slot
            ? \Carbon\Carbon::createFromTimeString($booking->slot->start_time)->format('g:i A')
            : '—';

        $message = "ArenaBook Reminder: Your booking ({$booking->booking_ref}) at {$booking->venue?->name} is tomorrow {$slotDate} at {$slotTime}. Contact us if needed.";

        $this->sendSms($customer->phone, $message, $booking);
    }

    /**
     * Send cancellation notice to customer.
     */
    public function sendCancellationNotice(Booking $booking, string $reason): void
    {
        $customer = $booking->customer;
        if (! $customer) return;

        $message = "ArenaBook: Your booking {$booking->booking_ref} has been cancelled. Reason: {$reason}. Contact us for assistance.";

        $this->sendSms($customer->phone, $message, $booking);
    }

    /**
     * Send payment received receipt.
     */
    public function sendPaymentReceipt(\App\Models\Payment $payment): void
    {
        $booking  = $payment->booking;
        $customer = $booking?->customer;
        if (! $customer) return;

        $amount  = number_format($payment->amount, 2);
        $due     = number_format(max(0, $booking->total_amount - $booking->paid_amount), 2);
        $message = "ArenaBook: Payment of ৳{$amount} received for {$booking->booking_ref}. Balance due: ৳{$due}. Thank you!";

        $this->sendSms($customer->phone, $message, $booking);
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private function buildConfirmationMessage(Booking $booking): string
    {
        $date = $booking->slot?->date?->format('d M Y') ?? '—';
        $time = $booking->slot
            ? \Carbon\Carbon::createFromTimeString($booking->slot->start_time)->format('g:i A')
            : '—';

        return "ArenaBook: Booking confirmed! Ref: {$booking->booking_ref}. Venue: {$booking->venue?->name}. Date: {$date} at {$time}. Total: ৳{$booking->total_amount}. Thank you!";
    }

    /**
     * Send SMS via SSL Wireless gateway.
     */
    private function sendSms(string $phone, string $message, ?Booking $booking = null): void
    {
        if (! config('arenabook.sms_enabled', false)) {
            Log::info("[SMS Skipped] To: {$phone} | Msg: {$message}");
            return;
        }

        $params = [
            'user'    => config('arenabook.sms_user'),
            'pass'    => config('arenabook.sms_pass'),
            'sid'     => config('arenabook.sms_sid'),
            'stype'   => 'normal',
            'msisdn'  => $phone,
            'msg'     => $message,
        ];

        try {
            $url      = 'https://gw.ssl.com.bd/api/make.aspx?' . http_build_query($params);
            $response = file_get_contents($url);

            Log::info("[SMS Sent] To: {$phone} | Response: {$response}");
        } catch (\Throwable $e) {
            Log::error("[SMS Failed] To: {$phone} | Error: {$e->getMessage()}");
        }
    }
}
