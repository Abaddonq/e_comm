<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f9fafb; padding: 30px; border-radius: 8px;">
        <h1 style="color: #111827; margin-bottom: 10px;">Order Confirmation</h1>
        <p style="color: #6b7280; margin-bottom: 30px;">Thank you for your order!</p>

        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h2 style="color: #111827; margin-top: 0; font-size: 18px;">Order Details</h2>
            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h2 style="color: #111827; margin-top: 0; font-size: 18px;">Items</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <th style="text-align: left; padding: 8px 0;">Product</th>
                        <th style="text-align: center; padding: 8px 0;">Qty</th>
                        <th style="text-align: right; padding: 8px 0;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 12px 0;">
                            <strong>{{ $item->product_title }}</strong>
                            @if($item->variant_sku)
                            <br><small style="color: #6b7280;">SKU: {{ $item->variant_sku }}</small>
                            @endif
                        </td>
                        <td style="text-align: center; padding: 12px 0;">{{ $item->quantity }}</td>
                        <td style="text-align: right; padding: 12px 0;">₺{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 5px 0;">Subtotal</td>
                    <td style="text-align: right;">₺{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;">Shipping</td>
                    <td style="text-align: right;">₺{{ number_format($order->shipping_cost, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;">Tax</td>
                    <td style="text-align: right;">₺{{ number_format($order->tax, 2) }}</td>
                </tr>
                <tr style="border-top: 2px solid #e5e7eb; font-weight: bold;">
                    <td style="padding: 10px 0;">Total</td>
                    <td style="text-align: right;">₺{{ number_format($order->total, 2) }}</td>
                </tr>
            </table>
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
    </div>

    <p style="text-align: center; color: #9ca3af; font-size: 14px; margin-top: 30px;">
        &copy; {{ date('Y') }} DecorMotto. All rights reserved.
    </p>
</body>
</html>
