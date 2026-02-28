<?php

namespace App\Exceptions;

class PaymentProcessingException extends DomainException
{
    public function __construct(string $message = 'Payment processing failed', ?string $orderId = null, ?array $gatewayResponse = null)
    {
        $this->orderId = $orderId;
        $this->gatewayResponse = $gatewayResponse;
        
        parent::__construct($message);
    }

    public function getOrderId(): ?string
    {
        return $this->orderId ?? null;
    }

    public function getGatewayResponse(): ?array
    {
        return $this->gatewayResponse ?? null;
    }

    private ?string $orderId = null;
    private ?array $gatewayResponse = null;
}
