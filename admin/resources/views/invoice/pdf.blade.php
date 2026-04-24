<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            background: #ffffff;
            padding: 40px;
        }

        /* ── Header ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 3px solid #1a1a1a;
            padding-bottom: 16px;
        }
        .header-title {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #1a1a1a;
        }
        .header-meta {
            text-align: right;
            line-height: 1.6;
        }
        .header-meta .inv-num {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a1a;
        }
        .header-meta .inv-date {
            font-size: 12px;
            color: #555;
        }

        /* ── Billing block ── */
        .billing {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .billing-block {
            width: 48%;
            line-height: 1.7;
        }
        .billing-block .label {
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #555;
            margin-bottom: 4px;
        }
        .billing-block .value {
            font-size: 13px;
            color: #1a1a1a;
        }
        .billing-block.right {
            text-align: right;
        }

        /* ── Items table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        thead tr {
            background-color: #1a1a1a;
            color: #ffffff;
        }
        thead th {
            padding: 10px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 600;
        }
        thead th.left  { text-align: left; }
        thead th.right { text-align: right; }
        thead th.center { text-align: center; }

        tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        tbody td {
            padding: 9px 12px;
            font-size: 13px;
            color: #333;
        }
        tbody td.right  { text-align: right; }
        tbody td.center { text-align: center; }

        /* ── Totals ── */
        .totals {
            width: 100%;
            margin-top: 8px;
        }
        .totals td {
            padding: 6px 12px;
            font-size: 13px;
        }
        .totals .total-label {
            text-align: right;
            color: #555;
            font-weight: 600;
        }
        .totals .total-value {
            text-align: right;
            font-weight: 700;
            color: #1a1a1a;
            width: 130px;
        }
        .grand-total-row td {
            font-size: 15px;
            border-top: 2px solid #1a1a1a;
            padding-top: 10px;
        }

        /* ── Footer ── */
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            padding-top: 14px;
        }
    </style>
</head>
<body>

    {{-- ── Header ── --}}
    <div class="header">
        <div class="header-title">INVOICE</div>
        <div class="header-meta">
            <div class="inv-num">{{ $invoiceNumber }}</div>
            <div class="inv-date">Date: {{ $generatedAt }}</div>
        </div>
    </div>

    {{-- ── Billing block ── --}}
    <div class="billing" style="display: table; width: 100%; margin-bottom: 30px;">
        <div class="billing-block" style="display: table-cell; width: 50%;">
            <div class="label">Billed To</div>
            <div class="value"><strong>{{ $user->name }}</strong></div>
            <div class="value">{{ $user->email }}</div>
            <div class="value">{{ $order->phone }}</div>
            <div class="value" style="margin-top: 8px;">
                <div class="label">Shipping Address</div>
                <div class="value" style="font-size: 11px; color: #555;">{{ $order->shipping_address }}</div>
            </div>
        </div>
        <div class="billing-block right" style="display: table-cell; width: 50%; text-align: right;">
            <div class="label">Payment Details</div>
            <div class="value">Method: <span style="text-transform: uppercase; font-weight: bold;">{{ $paymentMethod }}</span></div>
            <div class="value" style="margin-top: 10px;">
                <div class="label">From</div>
                <div class="value"><strong>{{ config('company.name', 'WOSS Store') }}</strong></div>
                <div class="value">{{ config('company.email', 'support@woss.com') }}</div>
            </div>
        </div>
    </div>

    {{-- ── Items table ── --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
        <thead>
            <tr style="background-color: #1a1a1a; color: #ffffff;">
                <th style="padding: 10px 12px; text-align: left;">#</th>
                <th style="padding: 10px 12px; text-align: left;">Item</th>
                <th style="padding: 10px 12px; text-align: right;">Price</th>
                <th style="padding: 10px 12px; text-align: center;">Qty</th>
                <th style="padding: 10px 12px; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
            <tr style="border-bottom: 1px solid #e0e0e0;">
                <td style="padding: 9px 12px;">{{ $index + 1 }}</td>
                <td style="padding: 9px 12px;">{{ $item->product->name ?? 'Product Unavailable' }}</td>
                <td style="padding: 9px 12px; text-align: right;">{{ Number::currency($item->price, 'INR') }}</td>
                <td style="padding: 9px 12px; text-align: center;">{{ $item->quantity }}</td>
                <td style="padding: 9px 12px; text-align: right;">{{ Number::currency($item->price * $item->quantity, 'INR') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── Totals ── --}}
    <table class="totals" style="width: 100%; border-collapse: collapse;">
        <tr class="grand-total-row">
            <td style="text-align: right; padding: 10px 12px; font-weight: 600; color: #555;">Grand Total</td>
            <td style="text-align: right; padding: 10px 12px; font-weight: 700; color: #1a1a1a; width: 130px; border-top: 2px solid #1a1a1a; font-size: 16px;">
                {{ Number::currency($grandTotal, 'INR') }}
            </td>
        </tr>
    </table>

    {{-- ── Footer ── --}}
    <div class="footer" style="margin-top: 50px; text-align: center; border-top: 1px solid #e0e0e0; padding-top: 20px; color: #999; font-size: 11px;">
        Invoice generated automatically on {{ $generatedAt }}<br>
        Thank you for your purchase &mdash; {{ config('company.name', 'WOSS Store') }}
    </div>

</body>
</html>
