<?php

namespace App\Services;

use App\Jobs\SendShipmentNotificationEmail;
use App\Models\Address;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    protected CourierServiceInterface $courier;

    public function __construct(?CourierServiceInterface $courier = null)
    {
        $this->courier = $courier ?? new ManualCourier();
    }

    public function createShipmentForOrder(Order $order): Shipment
    {
        $shipment = Shipment::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'courier_name' => 'Manual',
        ]);

        Log::info('Shipment created', [
            'order_id' => $order->id,
            'shipment_id' => $shipment->id,
        ]);

        return $shipment;
    }

    public function updateShipmentStatus(Shipment $shipment, string $status): Shipment
    {
        $updateData = ['status' => $status];

        if ($status === 'shipped') {
            $updateData['shipped_at'] = now();
        }

        if ($status === 'delivered') {
            $updateData['delivered_at'] = now();
        }

        $shipment->update($updateData);

        Log::info('Shipment status updated', [
            'shipment_id' => $shipment->id,
            'status' => $status,
        ]);

        return $shipment->fresh();
    }

    public function addTrackingNumber(Shipment $shipment, string $trackingNumber): Shipment
    {
        $shipment->update([
            'tracking_number' => $trackingNumber,
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);

        $order = $shipment->order;
        
        if ($order->user) {
            SendShipmentNotificationEmail::dispatch(
                $order,
                $trackingNumber,
                $shipment->courier_name
            );
        }

        Log::info('Tracking number added', [
            'shipment_id' => $shipment->id,
            'tracking_number' => $trackingNumber,
        ]);

        return $shipment->fresh();
    }

    public function calculateShippingCost(Address $address): float
    {
        return $this->courier->calculateShippingCost($address);
    }

    public function cancelShipment(Shipment $shipment): bool
    {
        if ($shipment->tracking_number) {
            return $this->courier->cancelShipment($shipment->tracking_number);
        }

        $shipment->update(['status' => 'cancelled']);

        return true;
    }

    public function getTrackingInfo(Shipment $shipment): ?array
    {
        if (!$shipment->tracking_number) {
            return null;
        }

        return $this->courier->getTrackingInfo($shipment->tracking_number);
    }
}
