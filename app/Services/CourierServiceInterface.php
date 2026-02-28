<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\Log;

interface CourierServiceInterface
{
    public function createShipment(array $shipmentData): array;

    public function getTrackingInfo(string $trackingNumber): array;

    public function cancelShipment(string $trackingNumber): bool;

    public function calculateShippingCost(Address $address): float;
}
