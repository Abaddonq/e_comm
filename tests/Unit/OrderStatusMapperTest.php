<?php

namespace Tests\Unit;

use App\Support\OrderStatusMapper;
use PHPUnit\Framework\TestCase;

class OrderStatusMapperTest extends TestCase
{
    public function test_it_maps_legacy_statuses_to_professional_domains(): void
    {
        $mapped = OrderStatusMapper::mapLegacyStatus('processing');

        $this->assertSame(OrderStatusMapper::FULFILLMENT_PROCESSING, $mapped['fulfillment_status']);
        $this->assertSame(OrderStatusMapper::PAYMENT_PAID, $mapped['payment_status']);
        $this->assertSame(OrderStatusMapper::RETURN_NONE, $mapped['return_status']);
    }

    public function test_it_maps_fulfillment_status_back_to_legacy_status(): void
    {
        $this->assertSame('processing', OrderStatusMapper::legacyStatusForFulfillment(OrderStatusMapper::FULFILLMENT_PACKED));
        $this->assertSame('shipped', OrderStatusMapper::legacyStatusForFulfillment(OrderStatusMapper::FULFILLMENT_OUT_FOR_DELIVERY));
    }
}
