<?php

namespace App\Exceptions;

use App\Enums\BookingStatus;
use RuntimeException;

class InvalidBookingTransitionException extends RuntimeException
{
    public function __construct(
        BookingStatus|string $from = '',
        BookingStatus|string $to   = '',
    ) {
        $fromLabel = $from instanceof BookingStatus ? $from->label() : $from;
        $toLabel   = $to   instanceof BookingStatus ? $to->label()   : $to;

        $message = ($fromLabel && $toLabel)
            ? "Cannot transition booking from '{$fromLabel}' to '{$toLabel}'."
            : 'This status transition is not allowed.';

        parent::__construct($message);
    }
}

