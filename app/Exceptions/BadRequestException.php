<?php

namespace App\Exceptions;

use Exception;

class BadRequestException extends Exception
{
    public function __construct(protected $message)
    {
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        abort(400, $this->message);
    }
}
