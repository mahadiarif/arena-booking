<?php

namespace App\Exceptions;

use RuntimeException;

class SlotNotAvailableException extends RuntimeException
{
    public function __construct(string $message = 'The selected slot is no longer available.')
    {
        parent::__construct($message);
    }

    public function render()
    {
        return back()->withErrors(['slot_id' => $this->getMessage()]);
    }
}
