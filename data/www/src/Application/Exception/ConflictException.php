<?php

namespace App\Application\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ConflictException extends \Exception
{
    public function __construct($message = "", $code = Response::HTTP_CONFLICT, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
