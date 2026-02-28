<?php

namespace App\Exceptions;

use Exception;

class OrderCreationException extends DomainException
{
    protected array $context;

    public function __construct(string $message = "Order creation failed", array $context = [], int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
