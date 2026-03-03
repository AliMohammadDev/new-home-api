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

        .totals-table {
            width: 400px;
            border-top: 2px solid #eee;
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
                <h1>بيان مبيعات إجمالي</h1>
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

    <table style="width: 100%; margin-bottom: 40px;">
        <tr>
            <td style="text-align: right; width: 50%;">
                <h4 style="color: #888; margin-bottom: 5px; font-size: 12pt;">ملخص العملية:</h4>
                <strong style="font-size: 18pt;">تقرير مبيعات الكاشير</strong><br>
                <span>عدد الفواتير المشمولة: {{ count(request('ids', [])) }} فاتورة</span>
            </td>
            <td style="text-align: left; vertical-align: top;">
                <h4 style="color: #888; margin-bottom: 5px; font-size: 12pt;">تفاصيل المستند:</h4>
                <span>تاريخ الطباعة: {{ now()->format('Y/m/d') }}</span><br>
                <span>الحالة: <span style="color: #025043; font-weight: bold;">تقرير نهائي</span></span>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%;">رقم الفاتورة</th>
                <th style="width: 35%;">المنتج</th>
                <th style="text-align: center;">الكمية</th>
                <th style="text-align: center;">سعر الوحدة</th>
                <th style="text-align: left;">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @php $totalQuantity = 0; @endphp
            @foreach ($salesItems as $item)
                @php $totalQuantity += $item->quantity; @endphp
                <tr>
                    <td style="text-align: center; color: #666;">
                        #{{ $item->cashier_sales_fatora_id }}
                    </td>
                    <td>
                        <strong style="color: #025043;">
                            {{ $item->variant->product->name['ar'] ?? ($item->variant->product->name['en'] ?? 'منتج غير معروف') }}
                        </strong>
                        <br>
                        <small style="color: #666;">SKU: {{ $item->variant->sku }} | كاشير:
                            {{ $item->fatora->cashier->user->name ?? '-' }}</small>
                    </td>
                    <td style="text-align: center;">
                        {{ number_format($item->quantity) }}
                    </td>
                    <td style="text-align: center;">
                        {{ number_format($item->price, 2) }} $
                    </td>
                    <td style="text-align: left; font-weight: bold;">
                        {{ number_format($item->full_price, 2) }} $
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table" align="left">
        <tr>
            <td style="color: #666;">إجمالي عدد الأصناف:</td>
            <td style="text-align: left;">{{ $salesItems->count() }} بند</td>
        </tr>
        <tr>
            <td style="color: #666;">إجمالي القطع المبيعة:</td>
            <td style="text-align: left;">{{ number_format($totalQuantity) }} قطعة</td>
        </tr>
        <tr class="total-row final">
            <td style="padding-top: 15px;">المجموع الكلي:</td>
            <td style="text-align: left; padding-top: 15px;">{{ number_format($totalAmount, 2) }} $</td>
        </tr>
    </table>

    <div style="clear: both; margin-top: 60px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
        <p style="color: #666; font-size: 12pt;">تم توليد هذا التقرير آلياً لمراجعة مبيعات نقاط البيع.</p>
    </div>

</body>

</html>
