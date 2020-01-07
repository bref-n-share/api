<?php

namespace App\Domain\Core\Exception;

use Throwable;

abstract class AbstractException extends \Exception
{
    public function __construct($message = "", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->getCode();
    }
}
