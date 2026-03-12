<?php

namespace App\Support;

class OrderStatusMapper
{
    public const FULFILLMENT_PENDING = 'pending';
    public const FULFILLMENT_PROCESSING = 'processing';
    public const FULFILLMENT_PACKED = 'packed';
    public const FULFILLMENT_SHIPPED = 'shipped';
    public const FULFILLMENT_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const FULFILLMENT_DELIVERED = 'delivered';
    public const FULFILLMENT_CANCELLED = 'cancelled';

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_CANCELLED = 'cancelled';
    public const PAYMENT_REFUNDED = 'refunded';
    public const PAYMENT_PARTIALLY_REFUNDED = 'partially_refunded';

    public const RETURN_NONE = 'none';
    public const RETURN_REQUESTED = 'requested';
    public const RETURN_APPROVED = 'approved';
    public const RETURN_REJECTED = 'rejected';
    public const RETURN_RETURNED = 'returned';
    public const RETURN_REFUNDED = 'refunded';

    public static function mapLegacyStatus(string $legacyStatus): array
    {
        return match ($legacyStatus) {
            'pending' => [
                'fulfillment_status' => self::FULFILLMENT_PENDING,
                'payment_status' => self::PAYMENT_PENDING,
                'return_status' => self::RETURN_NONE,
            ],
            'paid' => [
                'fulfillment_status' => self::FULFILLMENT_PENDING,
                'payment_status' => self::PAYMENT_PAID,
                'return_status' => self::RETURN_NONE,
            ],
            'processing' => [
                'fulfillment_status' => self::FULFILLMENT_PROCESSING,
                'payment_status' => self::PAYMENT_PAID,
                'return_status' => self::RETURN_NONE,
            ],
            'shipped' => [
                'fulfillment_status' => self::FULFILLMENT_SHIPPED,
                'payment_status' => self::PAYMENT_PAID,
                'return_status' => self::RETURN_NONE,
            ],
            'delivered' => [
                'fulfillment_status' => self::FULFILLMENT_DELIVERED,
                'payment_status' => self::PAYMENT_PAID,
                'return_status' => self::RETURN_NONE,
            ],
            'cancelled' => [
                'fulfillment_status' => self::FULFILLMENT_CANCELLED,
                'payment_status' => self::PAYMENT_CANCELLED,
                'return_status' => self::RETURN_NONE,
            ],
            'refunded' => [
                'fulfillment_status' => self::FULFILLMENT_CANCELLED,
                'payment_status' => self::PAYMENT_REFUNDED,
                'return_status' => self::RETURN_REFUNDED,
            ],
            default => [
                'fulfillment_status' => self::FULFILLMENT_PENDING,
                'payment_status' => self::PAYMENT_PENDING,
                'return_status' => self::RETURN_NONE,
            ],
        };
    }

    public static function legacyStatusForFulfillment(string $fulfillmentStatus): string
    {
        return match ($fulfillmentStatus) {
            self::FULFILLMENT_PENDING => 'pending',
            self::FULFILLMENT_PROCESSING, self::FULFILLMENT_PACKED => 'processing',
            self::FULFILLMENT_SHIPPED, self::FULFILLMENT_OUT_FOR_DELIVERY => 'shipped',
            self::FULFILLMENT_DELIVERED => 'delivered',
            self::FULFILLMENT_CANCELLED => 'cancelled',
            default => 'pending',
        };
    }

    public static function fulfillmentStatuses(): array
    {
        return [
            self::FULFILLMENT_PENDING,
            self::FULFILLMENT_PROCESSING,
            self::FULFILLMENT_PACKED,
            self::FULFILLMENT_SHIPPED,
            self::FULFILLMENT_OUT_FOR_DELIVERY,
            self::FULFILLMENT_DELIVERED,
            self::FULFILLMENT_CANCELLED,
        ];
    }

    public static function adminStatusOptions(): array
    {
        return [
            self::FULFILLMENT_PENDING => 'Awaiting Payment / Review',
            self::FULFILLMENT_PROCESSING => 'In Fulfillment',
            self::FULFILLMENT_PACKED => 'Packed and Ready',
            self::FULFILLMENT_SHIPPED => 'In Transit',
            self::FULFILLMENT_OUT_FOR_DELIVERY => 'Last-Mile Delivery',
            self::FULFILLMENT_DELIVERED => 'Delivery Completed',
            self::FULFILLMENT_CANCELLED => 'Order Cancelled',
        ];
    }

    public static function customerStatusLabel(string $fulfillmentStatus): string
    {
        return match ($fulfillmentStatus) {
            self::FULFILLMENT_PENDING => 'Payment pending',
            self::FULFILLMENT_PROCESSING => 'Preparing your order',
            self::FULFILLMENT_PACKED => 'Packed and ready to ship',
            self::FULFILLMENT_SHIPPED => 'Shipped',
            self::FULFILLMENT_OUT_FOR_DELIVERY => 'Out for delivery',
            self::FULFILLMENT_DELIVERED => 'Delivered',
            self::FULFILLMENT_CANCELLED => 'Cancelled',
            default => 'In progress',
        };
    }

    public static function badgeClass(string $fulfillmentStatus): string
    {
        return match ($fulfillmentStatus) {
            self::FULFILLMENT_PENDING => 'bg-yellow-100 text-yellow-800',
            self::FULFILLMENT_PROCESSING, self::FULFILLMENT_PACKED => 'bg-blue-100 text-blue-800',
            self::FULFILLMENT_SHIPPED => 'bg-purple-100 text-purple-800',
            self::FULFILLMENT_OUT_FOR_DELIVERY => 'bg-indigo-100 text-indigo-800',
            self::FULFILLMENT_DELIVERED => 'bg-green-100 text-green-800',
            self::FULFILLMENT_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
