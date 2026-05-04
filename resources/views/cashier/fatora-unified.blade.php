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
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            /* تصغير حجم الخط العام ليعطي طابع رسمي */
            font-size: 11pt;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #025043;
            margin-bottom: 25px;
            padding-bottom: 15px;
        }

        .document-title h1 {
            font-size: 24pt;
            margin: 0;
            color: #025043;
            font-weight: 700;
        }

        /* تنسيق جدول المحتويات */
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #025043;
            color: #ffffff;
            padding: 10px;
            text-align: right;
            font-size: 11pt;
            border: 1px solid #025043;
        }

        .items-table td {
            padding: 10px;
            border: 1px solid #eee;
            font-size: 10.5pt;
            vertical-align: middle;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .totals-table {
            width: 320px;
            /* تقليل العرض ليكون متناسق */
            float: left;
            /* ليبقى جهة اليسار في نظام RTL */
        }

        .totals-table td {
            padding: 8px 10px;
            font-size: 11pt;
        }

        .total-row.final {
            background-color: #025043;
            color: #fff;
            font-weight: 700;
            font-size: 16pt;
        }

        .total-row.final td {
            padding: 12px 10px;
        }

        small {
            font-size: 9pt;
            color: #777;
        }

        strong {
            font-weight: 600;
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
                    <img src="{{ $logoPath }}" width="120">
                @else
                    <h2 style="color: #025043; margin: 0;">STORE NAME</h2>
                @endif
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-bottom: 30px;">
        <tr>
            <td style="text-align: right; width: 50%; vertical-align: top;">
                <span style="color: #666; font-size: 10pt;">ملخص العملية:</span><br>
                <strong style="font-size: 14pt; color: #025043;">تقرير مبيعات الكاشير</strong><br>
                <div style="margin-top: 5px;">
                    <span>اسم الكاشير:
                        <strong>{{ $salesItems->first()->fatora->cashier->user->name ?? 'غير محدد' }}</strong></span><br>
                    <span>عدد الفواتير المشمولة: <strong>{{ count(request('ids', [])) }}</strong></span>
                </div>
            </td>
            <td style="text-align: left; vertical-align: top; width: 50%;">
                <span style="color: #666; font-size: 10pt;">تفاصيل المستند:</span><br>
                <span>تاريخ الطباعة: <strong>{{ now()->format('Y/m/d') }}</strong></span><br>
                <span>الحالة: <span style="color: #025043; font-weight: bold;">تقرير نهائي</span></span>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 12%; text-align: center;">رقم الفاتورة</th>
                <th style="width: 40%;">المنتج</th>
                <th style="text-align: center; width: 10%;">الكمية</th>
                <th style="text-align: center; width: 18%;">سعر الوحدة</th>
                <th style="text-align: left; width: 20%;">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @php $totalQuantity = 0; @endphp
            @foreach ($salesItems as $item)
                @php $totalQuantity += $item->quantity; @endphp
                <tr>
                    <td style="text-align: center; color: #555;">
                        #{{ $item->cashier_sales_fatora_id }}
                    </td>
                    <td>
                        <div style="font-weight: 600; color: #025043;">
                            {{ $item->variant->product->name['ar'] ?? ($item->variant->product->name['en'] ?? 'منتج غير معروف') }}
                        </div>
                        <small>SKU: {{ $item->variant->sku }}</small>
                    </td>
                    <td style="text-align: center;">
                        {{ number_format($item->quantity) }}
                    </td>
                    <td style="text-align: center;">
                        {{ number_format($item->price, 2) }} $
                    </td>
                    <td style="text-align: left; font-weight: 700;">
                        {{ number_format($item->full_price, 2) }} $
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table" align="left">
        <tr>
            <td>إجمالي القطع المبيعة:</td>
            <td style="text-align: left; font-weight: 600;">{{ number_format($totalQuantity) }} قطعة</td>
        </tr>
        <tr class="total-row">
            <td>المجموع الكلي:</td>
            <td style="text-align: left;">{{ number_format($totalAmount, 2) }} $</td>
        </tr>
    </table>

</body>

</html>
