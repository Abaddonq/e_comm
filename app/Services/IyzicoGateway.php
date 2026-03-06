<?php

namespace App\Services;

use Iyzipay\Options;
use Iyzipay\Request\CreatePaymentRequest;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\Payment;
use Iyzipay\Model\Locale;
use Iyzipay\Model\Currency;
use Iyzipay\Model\PaymentChannel;
use Iyzipay\Model\PaymentGroup;
use Illuminate\Support\Facades\Log;

class IyzicoGateway implements PaymentGatewayInterface
{
    protected Options $options;

    public function __construct()
    {
        $this->options = new Options();
        $this->options->setApiKey(config('payment.iyzico.api_key'));
        $this->options->setSecretKey(config('payment.iyzico.secret_key'));
        $this->options->setBaseUrl(config('payment.iyzico.base_url', 'https://sandbox-api.iyzipay.com'));
    }

    public function initiate(array $paymentData): array
    {
        try {
            $request = new CreatePaymentRequest();
            $request->setLocale(app()->getLocale() === 'tr' ? Locale::TR : Locale::EN);
            $request->setConversationId((string) $paymentData['order_id']);
            $request->setPrice($this->formatAmount($paymentData['amount']));
            $request->setPaidPrice($this->formatAmount($paymentData['amount']));
            $request->setCurrency(Currency::TL);
            $request->setBasketId((string) $paymentData['order_id']);
            $request->setPaymentChannel(PaymentChannel::WEB);
            $request->setPaymentGroup(PaymentGroup::PRODUCT);

            $cardNumber = $this->sanitizeCardNumber($paymentData['card_number'] ?? '');
            $cardType = $this->detectCardType($cardNumber);
            
            if ($cardType !== 'amex') {
                $request->setInstallment(1);
            }

            $paymentCard = new PaymentCard();
            $paymentCard->setCardHolderName($paymentData['card_holder_name'] ?? 'Customer');
            $paymentCard->setCardNumber($cardNumber);
            $paymentCard->setExpireMonth($paymentData['expire_month'] ?? '12');
            $paymentCard->setExpireYear($paymentData['expire_year'] ?? '2025');
            $paymentCard->setCvc($paymentData['cvv'] ?? '');
            $paymentCard->setRegisterCard(0);
            $request->setPaymentCard($paymentCard);

            $buyer = new Buyer();
            $buyer->setId((string) ($paymentData['user_id'] ?? 'guest'));
            $buyer->setName($paymentData['customer_name'] ?? 'Customer');
            $buyer->setSurname('Customer');
            $buyer->setIdentityNumber('11111111111');
            $buyer->setEmail($paymentData['customer_email'] ?? 'customer@example.com');
            $buyer->setGsmNumber($paymentData['customer_phone'] ?? '+905555555555');
            $buyer->setRegistrationAddress($paymentData['address'] ?? 'Address');
            $buyer->setIp($paymentData['ip'] ?? request()->ip());
            $buyer->setCity($paymentData['city'] ?? 'Istanbul');
            $buyer->setCountry('Turkey');
            $buyer->setZipCode($paymentData['postal_code'] ?? '34000');
            $request->setBuyer($buyer);

            $shippingAddress = new Address();
            $shippingAddress->setContactName($paymentData['shipping_name'] ?? $paymentData['customer_name'] ?? 'Customer');
            $shippingAddress->setCity($paymentData['city'] ?? 'Istanbul');
            $shippingAddress->setCountry('Turkey');
            $shippingAddress->setAddress($paymentData['address'] ?? 'Address');
            $shippingAddress->setZipCode($paymentData['postal_code'] ?? '34000');
            $request->setShippingAddress($shippingAddress);

            $billingAddress = new Address();
            $billingAddress->setContactName($paymentData['shipping_name'] ?? $paymentData['customer_name'] ?? 'Customer');
            $billingAddress->setCity($paymentData['city'] ?? 'Istanbul');
            $billingAddress->setCountry('Turkey');
            $billingAddress->setAddress($paymentData['address'] ?? 'Address');
            $billingAddress->setZipCode($paymentData['postal_code'] ?? '34000');
            $request->setBillingAddress($billingAddress);

            $basketItems = [];
            $itemsTotal = 0;
            foreach ($paymentData['items'] ?? [] as $item) {
                $basketItem = new BasketItem();
                $basketItem->setId((string) ($item['id'] ?? 'item_1'));
                $basketItem->setName($item['name'] ?? 'Product');
                $basketItem->setCategory1($item['category'] ?? 'General');
                $basketItem->setItemType('PHYSICAL');
                $itemPrice = $this->formatAmount($item['price'] * $item['quantity']);
                $basketItem->setPrice($itemPrice);
                $basketItems[] = $basketItem;
                $itemsTotal += $itemPrice;
            }

            $shippingCost = $this->formatAmount($paymentData['amount'] ?? 0) - $itemsTotal;
            if ($shippingCost > 0) {
                $basketItem = new BasketItem();
                $basketItem->setId('shipping');
                $basketItem->setName('Shipping Cost');
                $basketItem->setCategory1('Shipping');
                $basketItem->setItemType('VIRTUAL');
                $basketItem->setPrice($this->formatAmount($shippingCost));
                $basketItems[] = $basketItem;
            }

            $request->setBasketItems($basketItems);

            $payment = Payment::create($request, $this->options);

            Log::info('Iyzico payment response', [
                'status' => $payment->getStatus(),
                'errorMessage' => $payment->getErrorMessage(),
                'paymentId' => $payment->getPaymentId(),
                'cardLast4' => $this->maskCardForLogs($paymentData['card_number'] ?? ''),
            ]);

            if ($payment->getStatus() === 'success') {
                return [
                    'success' => true,
                    'transaction_id' => $payment->getPaymentId(),
                    'order_id' => $paymentData['order_id'],
                ];
            }

            Log::error('Iyzico payment failed', [
                'error' => $payment->getErrorMessage(),
                'order_id' => $paymentData['order_id']
            ]);

            return [
                'success' => false,
                'error' => $payment->getErrorMessage() ?? 'Payment failed',
            ];

        } catch (\Exception $e) {
            Log::error('Iyzico payment exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function verifyCallback(
        array $callbackData,
        ?string $rawPayload = null,
        ?string $signature = null,
        ?string $timestamp = null
    ): bool
    {
        $statusValid = isset($callbackData['paymentStatus'])
            && in_array($callbackData['paymentStatus'], ['SUCCESS', 'AUTHENTICATED'], true);

        $secret = (string) config('payment.iyzico.webhook_secret', '');
        $strict = (bool) config('payment.iyzico.webhook_strict', app()->environment('production'));

        if ($secret === '') {
            return $strict ? false : $statusValid;
        }

        if (!$rawPayload || !$signature) {
            return false;
        }

        if (!$this->verifyWebhookTimestamp($timestamp)) {
            return false;
        }

        if (!$this->verifyWebhookSignature($rawPayload, $signature, $timestamp, $secret)) {
            return false;
        }

        return $statusValid;
    }

    public function getTransactionId(array $callbackData): ?string
    {
        return $callbackData['paymentId'] ?? $callbackData['transactionId'] ?? null;
    }

    public function getPaymentStatus(array $callbackData): string
    {
        if (isset($callbackData['paymentStatus'])) {
            return match ($callbackData['paymentStatus']) {
                'SUCCESS' => 'completed',
                'FAILURE' => 'failed',
                'AUTHENTICATED' => 'pending',
                default => 'pending',
            };
        }
        return 'pending';
    }

    public function getFailureReason(array $callbackData): ?string
    {
        return $callbackData['errorMessage'] ?? $callbackData['failureReason'] ?? null;
    }

    protected function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    protected function sanitizeCardNumber(string $cardNumber): string
    {
        return preg_replace('/\s+/', '', $cardNumber);
    }

    protected function detectCardType(string $cardNumber): string
    {
        if (empty($cardNumber)) {
            return 'unknown';
        }
        
        $firstDigit = substr($cardNumber, 0, 1);
        $firstTwo = substr($cardNumber, 0, 2);
        $firstFour = substr($cardNumber, 0, 4);
        
        if ($firstTwo === '34' || $firstTwo === '37') {
            return 'amex';
        }
        
        if ($firstTwo === '51' || $firstTwo === '52' || $firstTwo === '53' || 
            $firstTwo === '54' || $firstTwo === '55' ||
            ($firstFour >= '2221' && $firstFour <= '2720')) {
            return 'mastercard';
        }
        
        if ($firstTwo === '40' || $firstTwo === '41' || $firstTwo === '42' || 
            $firstTwo === '43' || $firstTwo === '44' || $firstTwo === '45' ||
            $firstTwo === '46' || $firstTwo === '47' || $firstTwo === '48' || 
            $firstTwo === '49') {
            return 'visa';
        }
        
        return 'unknown';
    }

    protected function verifyWebhookTimestamp(?string $timestamp): bool
    {
        if (!$timestamp) {
            return false;
        }

        if (!ctype_digit($timestamp)) {
            return false;
        }

        $tolerance = (int) config('payment.iyzico.webhook_tolerance', 300);
        $delta = abs(time() - (int) $timestamp);

        return $delta <= $tolerance;
    }

    protected function verifyWebhookSignature(string $rawPayload, string $signature, ?string $timestamp, string $secret): bool
    {
        $signature = trim($signature);

        $candidates = [
            hash_hmac('sha256', $rawPayload, $secret),
        ];

        if ($timestamp) {
            $candidates[] = hash_hmac('sha256', $timestamp . '.' . $rawPayload, $secret);
            $candidates[] = hash_hmac('sha256', $rawPayload . '.' . $timestamp, $secret);
        }

        foreach ($candidates as $candidate) {
            if (hash_equals($candidate, $signature)) {
                return true;
            }
        }

        return false;
    }

    protected function maskCardForLogs(string $cardNumber): string
    {
        $sanitized = $this->sanitizeCardNumber($cardNumber);
        $last4 = substr($sanitized, -4);

        return $last4 ? ('****' . $last4) : 'masked';
    }
}
