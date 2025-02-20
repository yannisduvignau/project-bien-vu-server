<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{

    public function __construct(
        protected $message
    ) {
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        abort(422, $this->message);
    }
}
