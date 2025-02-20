<?php

namespace App\Exceptions;

use Exception;

class ResourceNotAuthorizedException extends Exception
{

    public function __construct(protected $message)
    {
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        abort(403, $this->message);
    }
}
