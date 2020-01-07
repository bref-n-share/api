<?php

namespace App\Domain\Core\Exception;

interface ExceptionInterface
{
    public function getStatusCode(): int;
}
