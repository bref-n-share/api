<?php

namespace App\Domain\Core\Exception;

use Throwable;

class ConflictException extends AbstractException
{
    public function __construct($message = "", $code = 409, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
