<?php

namespace App\Exceptions;

use Exception;

class DomainException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode(): string
    {
        return strtolower(class_basename($this));
    }
}
