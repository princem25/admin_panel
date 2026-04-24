<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { background: #fff; border: 1px solid #e5e7eb; padding: 24px; border-radius: 0 0 8px 8px; }
        .alert-badge { display: inline-block; background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600; }
        .stock-count { font-size: 48px; font-weight: bold; color: #d97706; text-align: center; margin: 16px 0; }
        .product-info { background: #f9fafb; border-radius: 6px; padding: 16px; margin: 16px 0; }
        .product-info dt { font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase; }
        .product-info dd { margin: 0 0 12px 0; font-size: 16px; }
        .footer { text-align: center; color: #9ca3af; font-size: 12px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">⚠️ Low Stock Alert</h1>
        <p style="margin: 8px 0 0;">Immediate attention required</p>
    </div>

    <div class="content">
        <p>Hello Admin,</p>

        <p>The following product is running low on stock and needs to be restocked soon:</p>

        <div class="stock-count">
            {{ $product->stock }} units left
        </div>

        <div class="product-info">
            <dl>
                <dt>Product Name</dt>
                <dd>{{ $product->name }}</dd>

                <dt>Product ID</dt>
                <dd>#{{ $product->id }}</dd>

                <dt>Category</dt>
                <dd>{{ $product->category->name ?? 'N/A' }}</dd>

                <dt>Current Price</dt>
                <dd>{{ Number::currency($product->price, 'INR') }}</dd>
            </dl>
        </div>

        <p><span class="alert-badge">⚠️ Stock is below 10 units</span></p>

        <p>Please restock this product at your earliest convenience to avoid losing potential sales.</p>
    </div>

    <div class="footer">
        <p>This is an automated alert from the Inventory Management System.</p>
    </div>
</body>
</html>
