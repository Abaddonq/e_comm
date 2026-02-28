<?php

namespace App\Exceptions;

class PaymentVerificationException extends DomainException
{
    protected $callbackData;

    public function __construct(string $message = 'Payment verification failed', array $callbackData = [])
    {
        parent::__construct($message);
        $this->callbackData = $callbackData;
    }

    public function getCallbackData(): array
    {
        return $this->callbackData;
    }
}
