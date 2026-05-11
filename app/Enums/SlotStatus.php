<?php

namespace App\Enums;

enum SlotStatus: string
{
    case Available = 'available';
    case Partial   = 'partial';
    case Booked    = 'booked';
    case Blocked   = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Partial   => 'Partial',
            self::Booked    => 'Booked',
            self::Blocked   => 'Blocked',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Available => 'bg-green-100 text-green-800 border-green-200',
            self::Partial   => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::Booked    => 'bg-red-100 text-red-800 border-red-200',
            self::Blocked   => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }

    public function isBookable(): bool
    {
        return in_array($this, [self::Available, self::Partial], true);
    }
}
