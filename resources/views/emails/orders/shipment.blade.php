<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Shipped</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f9fafb; padding: 30px; border-radius: 8px;">
        <h1 style="color: #111827; margin-bottom: 10px;">Your Order Has Been Shipped!</h1>
        <p style="color: #6b7280; margin-bottom: 30px;">We're happy to inform you that your order is on its way.</p>

        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h2 style="color: #111827; margin-top: 0; font-size: 18px;">Shipment Details</h2>
            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            @if($courierName)
            <p><strong>Courier:</strong> {{ $courierName }}</p>
            @endif
            <p><strong>Tracking Number:</strong> <span style="font-size: 16px; font-weight: bold; color: #4f46e5;">{{ $trackingNumber }}</span></p>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px;">
            <h2 style="color: #111827; margin-top: 0; font-size: 18px;">Shipping Address</h2>
            <p style="margin: 0;">
                {{ $order->shipping_full_name }}<br>
                {{ $order->shipping_address_line1 }}<br>
                @if($order->shipping_address_line2)
                    {{ $order->shipping_address_line2 }}<br>
                @endif
                {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                {{ $order->shipping_country }}
            </p>
        </div>

        <p style="text-align: center; margin-top: 30px; color: #6b7280;">
            You can track your shipment using the tracking number above.
        </p>
    </div>

    <p style="text-align: center; color: #9ca3af; font-size: 14px; margin-top: 30px;">
        &copy; {{ date('Y') }} DecorMotto. All rights reserved.
    </p>
</body>
</html>
