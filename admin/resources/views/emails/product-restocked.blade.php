<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { background: #fff; border: 1px solid #e5e7eb; padding: 24px; border-radius: 0 0 8px 8px; }
        .stock-badge { display: inline-block; background: #d1fae5; color: #065f46; padding: 6px 16px; border-radius: 16px; font-size: 14px; font-weight: 600; }
        .product-card { background: #f9fafb; border-radius: 8px; padding: 20px; margin: 16px 0; text-align: center; }
        .product-name { font-size: 22px; font-weight: bold; color: #111827; margin: 8px 0; }
        .product-price { font-size: 28px; font-weight: bold; color: #059669; }
        .cta-button { display: inline-block; background: #059669; color: white; padding: 12px 32px; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 16px; }
        .footer { text-align: center; color: #9ca3af; font-size: 12px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">🎉 Great News!</h1>
        <p style="margin: 8px 0 0;">A product you were waiting for is back in stock</p>
    </div>

    <div class="content">
        <p>Hello,</p>

        <p>Good news! A product from your waitlist is now available again:</p>

        <div class="product-card">
            <p class="product-name">{{ $product->name }}</p>
            <p class="product-price">₹{{ number_format($product->price, 2) }}</p>
            <p><span class="stock-badge">✅ Back in Stock — {{ $product->stock }} units available</span></p>
        </div>

        <p>Don't wait too long — stock may run out quickly!</p>

        <p style="text-align: center;">
            <a href="{{ url('/product/' . $product->id) }}" class="cta-button">🛍️ Shop Now</a>
        </p>
    </div>

    <div class="footer">
        <p>You received this email because you joined the waitlist for this product.</p>
        <p>If you no longer wish to receive these notifications, you can remove yourself from the waitlist.</p>
    </div>
</body>
</html>
