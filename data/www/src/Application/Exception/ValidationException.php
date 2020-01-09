<?php

namespace App\Application\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ValidationException extends \Exception
{
    private ConstraintViolationListInterface $violationList;

    public function __construct(
        ConstraintViolationListInterface $violationList,
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $this->violationList = $violationList;

        parent::__construct($message ?: $this->__toString(), $code, $previous);
    }

    public function __toString(): string
    {
        $message = '';
        foreach ($this->violationList as $violation) {
            if ('' !== $message) {
                $message .= "\n";
            }

            $message .= $violation->getMessage();
        }
        return $message;
    }
}
