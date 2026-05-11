<?php

namespace App\Exceptions;

use RuntimeException;

class AlreadyOnWaitlistException extends RuntimeException
{
    public function __construct(string $message = 'Customer is already on the waitlist for this slot.')
    {
        parent::__construct($message);
    }
}
