<?php

namespace App\Services;

use App\Models\Address;

class ManualCourier implements CourierServiceInterface
{
    public function createShipment(array $shipmentData): array
    {
        return [
            'success' => true,
            'tracking_number' => $shipmentData['tracking_number'] ?? 'MANUAL-' . strtoupper(uniqid()),
            'courier' => 'Manual',
            'status' => 'pending',
        ];
    }

    public function getTrackingInfo(string $trackingNumber): array
    {
        return [
            'tracking_number' => $trackingNumber,
            'status' => 'pending',
            'estimated_delivery' => null,
            'events' => [],
        ];
    }

    public function cancelShipment(string $trackingNumber): bool
    {
        return true;
    }

    public function calculateShippingCost(Address $address): float
    {
        $baseRate = config('shipping.base_rate', 29.90);
        
        $zoneRates = config('shipping.zone_rates', [
            'Turkey' => [
                'Istanbul' => 0,
                'Ankara' => 5,
                'Izmir' => 5,
                'default' => 10,
            ],
            'default' => 20,
        ]);

        $country = $address->country;
        $city = $address->city;

        if (isset($zoneRates[$country])) {
            if (is_array($zoneRates[$country])) {
                $cityRate = $zoneRates[$country][$city] ?? $zoneRates[$country]['default'] ?? 0;
                return $baseRate + $cityRate;
            }
            return $baseRate + $zoneRates[$country];
        }

        return $baseRate + ($zoneRates['default'] ?? 20);
    }
}
