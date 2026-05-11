<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Slot;
use App\Models\WaitlistEntry;
use Illuminate\Support\Facades\DB;

class WaitlistService
{
    /**
     * Add a customer to a slot's waitlist.
     * Position is auto-assigned as next in queue.
     */
    public function addToWaitlist(Slot $slot, Customer $customer): WaitlistEntry
    {
        // Prevent duplicate
        $existing = WaitlistEntry::where('slot_id', $slot->id)
            ->where('customer_id', $customer->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $nextPosition = WaitlistEntry::where('slot_id', $slot->id)->max('position') + 1;

        return WaitlistEntry::create([
            'slot_id'     => $slot->id,
            'customer_id' => $customer->id,
            'position'    => $nextPosition,
            'expires_at'  => now()->addHours(config('arenabook.waitlist_hold_hours', 24)),
        ]);
    }

    /**
     * Remove a customer from the waitlist and reorder positions.
     */
    public function removeFromWaitlist(WaitlistEntry $entry): void
    {
        DB::transaction(function () use ($entry) {
            $slotId   = $entry->slot_id;
            $position = $entry->position;

            $entry->delete();

            // Shift remaining entries up
            WaitlistEntry::where('slot_id', $slotId)
                ->where('position', '>', $position)
                ->decrement('position');
        });
    }

    /**
     * Notify the next customer in line when a slot opens up.
     */
    public function notifyNext(Slot $slot): ?WaitlistEntry
    {
        $next = WaitlistEntry::where('slot_id', $slot->id)
            ->whereNull('notified_at')
            ->orderBy('position')
            ->first();

        if ($next) {
            $next->update([
                'notified_at' => now(),
                'expires_at'  => now()->addHours(config('arenabook.waitlist_hold_hours', 2)),
            ]);

            // TODO: send notification to $next->customer
        }

        return $next;
    }

    /**
     * Expire waitlist entries that have passed their expiry time.
     * Returns count of expired entries processed.
     */
    public function expireEntries(): int
    {
        $expired = WaitlistEntry::whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        $count = $expired->count();

        foreach ($expired as $entry) {
            $slotId = $entry->slot_id;
            $this->removeFromWaitlist($entry);

            // Notify the new next in queue
            $slot = Slot::find($slotId);
            if ($slot) {
                $this->notifyNext($slot);
            }
        }

        return $count;
    }
}
