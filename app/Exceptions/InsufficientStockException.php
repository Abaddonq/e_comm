<?php

namespace App\Exceptions;

class InsufficientStockException extends DomainException
{
    protected $variantId;

    protected $requestedQuantity;

    protected $availableQuantity;

    public function __construct(
        string $message = 'Insufficient stock available',
        int $variantId = null,
        int $requestedQuantity = 0,
        int $availableQuantity = 0
    ) {
        parent::__construct($message);
        $this->variantId = $variantId;
        $this->requestedQuantity = $requestedQuantity;
        $this->availableQuantity = $availableQuantity;
    }

    public function getVariantId(): ?int
    {
        return $this->variantId;
    }

    public function getRequestedQuantity(): int
    {
        return $this->requestedQuantity;
    }

    public function getAvailableQuantity(): int
    {
        return $this->availableQuantity;
    }
}
