<?php

namespace App\Exceptions;

use Exception;

class WrongCredentialsException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        abort(401, __('auth.wrong_credentials'));
    }
}
