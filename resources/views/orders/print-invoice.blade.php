<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'cairo', sans-serif;
            direction: rtl;
            color: #222;
            line-height: 1.6;
            font-size: 14pt;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px solid #025043;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }

        .document-title h1 {
            font-size: 32pt;
            margin: 0;
            color: #025043;
            text-align: right;
        }

        .info-table {
            width: 100%;
            margin-bottom: 40px;
        }

        .info-box {
            width: 50%;
            vertical-align: top;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .items-table th {
            background-color: #025043;
            color: #ffffff;
            padding: 15px 10px;
            text-align: right;
            font-size: 16pt;
        }

        .items-table td {
            padding: 15px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 15pt;
        }

        .totals-container {
            width: 100%;
        }

        .totals-table {
            width: 350px;
            border-top: 2px solid #eee;
        }

        .totals-table td {
            padding: 10px;
            font-size: 16pt;
        }

        .total-row.final {
            background-color: #f8fcfb;
            color: #025043;
            font-weight: bold;
            font-size: 22pt;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td class="document-title" style="width: 50%; vertical-align: middle;">
                <h1>فاتورة مبيعات</h1>
                <p style="font-size: 18pt; margin: 0; text-align: right;">رقم الطلب: #{{ $order->id }}</p>
            </td>
            <td style="width: 50%; text-align: left; vertical-align: middle;">
                @php
                    $logoPath = public_path('images/logo.png');
                @endphp

                @if (file_exists($logoPath))
                    <img src="{{ $logoPath }}" width="150">
                @else
                    <h2 style="color: #025043;">STORE NAME</h2>
                @endif
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="info-box" style="text-align: right;">
                <h4 style="color: #888; margin-bottom: 5px; font-size: 12pt;">مُصدرة إلى:</h4>
                <strong style="font-size: 18pt;">{{ $order->user?->name ?? 'عميل مجهول' }}</strong><br>
                <span>العنوان: {{ $order->checkout?->address ?? 'غير محدد' }}</span>
            </td>
            <td class="info-box" style="text-align: left; vertical-align: top;">
                <h4 style="color: #888; margin-bottom: 5px; font-size: 12pt;">تفاصيل الوقت:</h4>
                <span>تاريخ الطلب: {{ $order->created_at->format('Y/m/d') }}</span><br>
                <span>الحالة: <span style="color: #025043; font-weight: bold;">مدفوع</span></span>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40%;">المنتج والنوع</th>
                <th style="text-align: center;">الكمية</th>
                <th style="text-align: center;">السعر</th>
                <th style="text-align: center;">الخصم</th>
                <th style="text-align: left;">المجموع</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $item)
                @php
                    $variant = $item->productVariant;
                    $hasDiscount = $variant->discount > 0;
                @endphp
                <tr>
                    <td>
                        <strong style="color: #025043;">
                            {{ $variant->product->name['ar'] ?? 'منتج غير معروف' }}
                        </strong>
                        <br>
                        <small style="color: #666;">
                            SKU: {{ $variant->sku }}
                        </small>
                    </td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>

                    <td style="text-align: center; vertical-align: middle;">
                        @if ($hasDiscount)
                            <span style="text-decoration: line-through; color: #999; font-size: 11pt; display: block;">
                                {{ number_format($variant->price, 2) }} $
                            </span>
                            <span style="color: #025043; font-weight: bold; font-size: 14pt; display: block;">
                                {{ number_format($variant->final_price, 2) }} $
                            </span>
                        @else
                            <span style="font-weight: bold; font-size: 14pt;">
                                {{ number_format($variant->price, 2) }} $
                            </span>
                        @endif
                    </td>

                    <td style="text-align: center; color: #e53e3e; font-weight: bold;">
                        {{ $hasDiscount ? floatval($variant->discount) . '%' : '-' }}
                    </td>

                    <td style="text-align: left; font-weight: bold; color: #025043;">
                        {{ number_format($item->total, 2) }} $
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-container">
        <table class="totals-table" align="left">
            <tr>
                <td style="color: #666;">المجموع الفرعي:</td>
                <td style="text-align: left;">
                    {{ number_format($order->total_amount - ($order->shipping_fee ?? 0), 2) }} $</td>
            </tr>
            @if ($order->shipping_fee)
                <tr>
                    <td style="color: #666;">رسوم الشحن:</td>
                    <td style="text-align: left;">{{ number_format($order->shipping_fee, 2) }} $</td>
                </tr>
            @endif
            <tr class="total-row final">
                <td style="padding-top: 15px;">الإجمالي الكلي:</td>
                <td style="text-align: left; padding-top: 15px;">{{ number_format($order->total_amount, 2) }} $</td>
            </tr>
        </table>
    </div>

    <div style="clear: both; margin-top: 60px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
        <p style="color: #666; font-size: 12pt;">شكراً لتعاملكم معنا! تم إصدار هذه الفاتورة إلكترونياً.</p>
    </div>

</body>

</html>
