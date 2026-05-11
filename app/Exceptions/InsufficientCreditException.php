<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientCreditException extends RuntimeException
{
    public function __construct(string $message = 'Insufficient credit balance.')
    {
        parent::__construct($message);
    }
}
