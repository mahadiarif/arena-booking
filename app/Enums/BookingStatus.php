<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case CheckedIn = 'checked_in';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow    = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Pending',
            self::Confirmed => 'Confirmed',
            self::CheckedIn => 'Checked In',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
            self::NoShow    => 'No Show',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending   => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::Confirmed => 'bg-blue-100 text-blue-800 border-blue-200',
            self::CheckedIn => 'bg-purple-100 text-purple-800 border-purple-200',
            self::Completed => 'bg-green-100 text-green-800 border-green-200',
            self::Cancelled => 'bg-red-100 text-red-800 border-red-200',
            self::NoShow    => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function canTransitionTo(self $new): bool
    {
        return match ($this) {
            self::Pending   => in_array($new, [self::Confirmed, self::Cancelled], true),
            self::Confirmed => in_array($new, [self::CheckedIn, self::Cancelled, self::NoShow], true),
            self::CheckedIn => $new === self::Completed,
            default         => false,
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled, self::NoShow], true);
    }
}
