<?php

namespace App\Contracts;

interface Validator
{
    /**
     * Run the validation rule.
     *
     * @throws \App\Exceptions\ValidationException
     * @throws \App\Exceptions\ResourceNotAuthorizedException
     */
    public static function validate($item): void;
}
