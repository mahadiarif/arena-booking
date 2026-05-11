<?php

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Booking $booking): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Booking $booking): bool
    {
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        // Staff: own booking, editable status, within 30 minutes of creation
        return (int) $booking->booked_by === $user->id
            && in_array($booking->status, [BookingStatus::Pending, BookingStatus::Confirmed], true)
            && $booking->created_at->gt(now()->subMinutes(30));
    }

    public function approve(User $user, Booking $booking): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($booking->status->isTerminal()) {
            return false;
        }

        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        // Staff: own booking within 30 minutes
        return (int) $booking->booked_by === $user->id
            && $booking->created_at->gt(now()->subMinutes(30));
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasRole('super_admin');
    }
}
