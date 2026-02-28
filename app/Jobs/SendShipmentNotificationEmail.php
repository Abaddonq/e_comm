<?php

namespace App\Jobs;

use App\Mail\ShipmentNotification;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendShipmentNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Order $order,
        public string $trackingNumber,
        public ?string $courierName = null
    ) {
    }

    public function handle(): void
    {
        $email = $this->order->user?->email ?? $this->order->shipping_full_name;

        Mail::to($email)->send(new ShipmentNotification(
            $this->order,
            $this->trackingNumber,
            $this->courierName
        ));
    }
}
