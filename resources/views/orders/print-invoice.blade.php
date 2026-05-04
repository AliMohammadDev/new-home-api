<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">


    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            font-size: 13pt;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px solid #025043;
            margin-bottom: 30px;
            padding-bottom: 15px;
        }

        .document-title h1 {
            font-size: 28pt;
            margin: 0;
            color: #025043;
            font-weight: 700;
        }

        .info-table {
            width: 100%;
            margin-bottom: 35px;
        }

        .info-box {
            width: 50%;
            vertical-align: top;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 35px;
        }

        .items-table th {
            background-color: #025043;
            color: #ffffff;
            padding: 12px;
            text-align: right;
            font-size: 13pt;
            border: 1px solid #025043;
        }

        .items-table td {
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 12pt;
            vertical-align: middle;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        .totals-container {
            width: 100%;
            margin-top: 25px;
        }

        .totals-table {
            width: 380px;
            float: left;
        }

        .totals-table td {
            padding: 8px 12px;
            font-size: 13pt;
        }

        .total-row.final {
            background-color: #025043;
            color: #ffffff;
            font-weight: 700;
            font-size: 18pt;
        }

        .total-row.final td {
            padding: 15px 12px;
        }

        small {
            font-size: 10pt;
            color: #666;
        }

        strong {
            font-weight: 700;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td class="document-title" style="width: 50%; vertical-align: middle;">
                <h1>فاتورة مبيعات</h1>
                <p style="font-size: 16pt; margin: 5px 0 0 0; color: #555; font-weight: 600;">رقم الطلب:
                    #{{ $order->id }}</p>
            </td>
            <td style="width: 50%; text-align: left; vertical-align: middle;">
                @php
                    $logoPath = public_path('images/logo.png');
                @endphp

                @if (file_exists($logoPath))
                    <img src="{{ $logoPath }}" width="140">
                @else
                    <h2 style="color: #025043; margin: 0; font-size: 20pt;">STORE NAME</h2>
                @endif
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="info-box" style="text-align: right;">
                <h4 style="color: #888; margin-bottom: 8px; font-size: 12pt; font-weight: normal;">معلومات المستلم:</h4>
                <strong style="font-size: 17pt; color: #025043;">
                    {{ $order->checkout?->first_name }} {{ $order->checkout?->last_name }}
                </strong><br>

                <div style="margin-top: 10px; font-size: 13pt;">
                    <span>المدينة: <strong>{{ $order->checkout?->city ?? 'غير محدد' }}</strong></span><br>
                    <span>الشارع: {{ $order->checkout?->street ?? '-' }}
                        @if ($order->checkout?->floor)
                            - طابق: {{ $order->checkout?->floor }}
                        @endif
                    </span><br>
                    <span>الهاتف: <strong dir="ltr"
                            style="letter-spacing: 1px;">{{ $order->checkout?->phone ?? '-' }}</strong></span>
                </div>
            </td>
            <td class="info-box" style="text-align: left; vertical-align: top;">
                <h4 style="color: #888; margin-bottom: 8px; font-size: 12pt; font-weight: normal;">تفاصيل الفاتورة:</h4>
                <span>تاريخ الطلب: <strong>{{ $order->created_at->format('Y/m/d') }}</strong></span><br>
                <span>طريقة الدفع:
                    <strong style="color: #025043;">
                        {{ $order->payment_method == 'cod' ? 'دفع عند الاستلام' : $order->payment_method }}
                    </strong>
                </span><br>
                <span>الحالة: <strong
                        style="color: #025043;">{{ $order->status == 'completed' ? 'مكتمل' : 'نشط' }}</strong></span>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40%;">المنتج والنوع</th>
                <th style="text-align: center; width: 10%;">الكمية</th>
                <th style="text-align: center; width: 15%;">السعر</th>
                <th style="text-align: center; width: 15%;">الخصم</th>
                <th style="text-align: left; width: 20%;">المجموع</th>
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
                        <div style="font-weight: 700; color: #025043; font-size: 13pt;">
                            {{ $variant->product->name['ar'] ?? 'منتج غير معروف' }}
                        </div>
                        <small>SKU: {{ $variant->sku }}</small>
                    </td>
                    <td style="text-align: center; font-weight: 600;">{{ $item->quantity }}</td>

                    <td style="text-align: center;">
                        @if ($hasDiscount)
                            <span style="text-decoration: line-through; color: #999; font-size: 10pt; display: block;">
                                {{ number_format($variant->price, 2) }} $
                            </span>
                            <span style="color: #025043; font-weight: 700; font-size: 13pt; display: block;">
                                {{ number_format($variant->final_price, 2) }} $
                            </span>
                        @else
                            <span style="font-weight: 700; font-size: 13pt;">
                                {{ number_format($variant->price, 2) }} $
                            </span>
                        @endif
                    </td>

                    <td style="text-align: center; color: #e53e3e; font-weight: 700; font-size: 12pt;">
                        {{ $hasDiscount ? floatval($variant->discount) . '%' : '-' }}
                    </td>

                    <td style="text-align: left; font-weight: 700; color: #025043; font-size: 13pt;">
                        {{ number_format($item->total, 2) }} $
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-container">
        @php
            $productsSubtotal = $order->total_amount - ($order->shipping_fee ?? 0) - ($order->delivery_fee ?? 0);
        @endphp

        <table class="totals-table" align="left">
            <tr>
                <td>مجموع المنتجات:</td>
                <td style="text-align: left; font-weight: 600;">{{ number_format($productsSubtotal, 2) }} $</td>
            </tr>

            @if ($order->shipping_fee > 0)
                <tr>
                    <td style="color: #666;">رسوم الشحن:</td>
                    <td style="text-align: left; font-weight: 600;">{{ number_format($order->shipping_fee, 2) }} $</td>
                </tr>
            @endif

            @if ($order->delivery_fee > 0)
                <tr>
                    <td style="color: #666;">رسوم التوصيل:</td>
                    <td style="text-align: left; font-weight: 600;">{{ number_format($order->delivery_fee, 2) }} $</td>
                </tr>
            @endif

            <tr class="total-row ">
                <td>الإجمالي الكلي:</td>
                <td style="text-align: left;">{{ number_format($order->total_amount, 2) }} $</td>
            </tr>
        </table>
    </div>

</body>

</html>
