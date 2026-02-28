<?php

namespace App\Services;

interface PaymentGatewayInterface
{
    public function initiate(array $paymentData): array;

    public function verifyCallback(array $callbackData): bool;

    public function getTransactionId(array $callbackData): ?string;

    public function getPaymentStatus(array $callbackData): string;

    public function getFailureReason(array $callbackData): ?string;
}
