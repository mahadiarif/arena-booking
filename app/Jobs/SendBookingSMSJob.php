<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendBookingSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(
        public Booking $booking,
        public string  $type
    ) {
        $this->onQueue('sms');
    }

    public function handle(): void
    {
        $phone = $this->booking->customer?->phone;

        if (! $phone) {
            Log::warning("SendBookingSMSJob: no phone for booking {$this->booking->booking_ref}");
            return;
        }

        $message = $this->buildMessage();

        try {
            $response = Http::timeout(10)->post(
                'https://sms.sslwireless.com/pushapi/dynamic/server.php',
                [
                    'user'   => config('arenabook.sms_user'),
                    'pass'   => config('arenabook.sms_pass'),
                    'sid'    => config('arenabook.sms_sid'),
                    'msisdn' => $phone,
                    'sms'    => $message,
                    'csmsid' => $this->booking->booking_ref . '-' . now()->timestamp,
                ]
            );

            if (! $response->successful()) {
                Log::warning("SMS API non-success for {$this->booking->booking_ref}: {$response->body()}");
            }
        } catch (\Throwable $e) {
            Log::error("SMS send failed for {$this->booking->booking_ref}: {$e->getMessage()}");
            $this->fail($e);
        }
    }

    private function buildMessage(): string
    {
        $booking = $this->booking;
        $ref     = $booking->booking_ref;
        $venue   = $booking->venue?->name ?? 'Venue';
        $date    = $booking->slot?->date?->format('d M Y') ?? '';
        $time    = $booking->slot?->start_time
            ? \Carbon\Carbon::createFromTimeString($booking->slot->start_time)->format('g:i A')
            : '';
        $amount  = '৳' . number_format($booking->total_amount, 0);

        return match ($this->type) {
            'confirmed' => "ArenaBook: Booking {$ref} confirmed for {$venue} on {$date} at {$time}. Amount: {$amount}.",
            'reminder'  => "ArenaBook: Reminder - Your booking {$ref} at {$venue} is tomorrow at {$time}.",
            'cancelled' => "ArenaBook: Booking {$ref} has been cancelled.",
            default     => "ArenaBook: Update on booking {$ref}.",
        };
    }
}
