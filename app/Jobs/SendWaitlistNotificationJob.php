<?php

namespace App\Jobs;

use App\Models\WaitlistEntry;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWaitlistNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 30;

    public function __construct(
        public WaitlistEntry $entry
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $entry    = $this->entry;
        $customer = $entry->customer;
        $slot     = $entry->slot;

        if (! $customer || ! $slot) {
            Log::warning("SendWaitlistNotificationJob: missing customer or slot for entry #{$entry->id}");
            return;
        }

        $holdMinutes = config('arenabook.waitlist_hold_minutes', 30);

        try {
            app(NotificationService::class)->sendWaitlistAlert($entry);
        } catch (\Throwable $e) {
            Log::error("Waitlist notification failed for entry #{$entry->id}: {$e->getMessage()}");
            $this->fail($e);
        }
    }
}
