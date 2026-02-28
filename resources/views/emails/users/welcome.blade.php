<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to DecorMotto</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4f46e5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background-color: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; background-color: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to DecorMotto!</h1>
    </div>
    <div class="content">
        <p>Hello {{ $user->name }},</p>
        
        <p>Thank you for registering with DecorMotto! We're excited to have you as a member of our community.</p>
        
        <p>With your new account, you can:</p>
        <ul>
            <li>Browse and purchase our beautiful home decoration products</li>
            <li>Save your favorite items to your wishlist</li>
            <li>Track your orders and view order history</li>
            <li>Save multiple shipping addresses for faster checkout</li>
        </ul>
        
        <p>Start shopping today and transform your living space with our curated collection of premium home decor!</p>
        
        <a href="{{ route('home') }}" class="button">Shop Now</a>
        
        <p>If you have any questions, feel free to reply to this email or contact our customer support team.</p>
        
        <p>Best regards,<br>The DecorMotto Team</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} DecorMotto. All rights reserved.</p>
    </div>
</body>
</html>
