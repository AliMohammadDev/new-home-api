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
                <h1>بيان شحن مخزون</h1>

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
                <h4 style="color: #888; margin-bottom: 5px; font-size: 12pt;">نقل إلى مستودع:</h4>
                <strong style="font-size: 18pt;">مجموعة المستودعات المحددة</strong><br>
                <span>نوع العملية: توزيع مخزون داخلي</span>
            </td>
            <td class="info-box" style="text-align: left; vertical-align: top;">
                <h4 style="color: #888; margin-bottom: 5px; font-size: 12pt;">تفاصيل الوقت:</h4>
                <span>تاريخ الاستخراج: {{ now()->format('Y/m/d') }}</span><br>
                <span>الحالة: <span style="color: #025043; font-weight: bold;">جاهز للنقل</span></span>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 45%;">المنتج والنوع</th>
                <th style="text-align: center;">المستودع الوجهة</th>
                <th style="text-align: center;">الكمية</th>
                <th style="text-align: left;">وقت الوصول</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @foreach ($records as $record)
                @php $totalAmount += $record->amount; @endphp
                <tr>
                    <td>
                        <strong
                            style="color: #025043;">{{ $record->productVariant->product->name['ar'] ?? 'منتج غير معروف' }}</strong>
                        <br>
                        <small style="color: #666;">
                            SKU: {{ $record->productVariant->sku }}
                        </small>
                    </td>
                    <td style="text-align: center;">{{ $record->warehouse->name }}</td>
                    <td style="text-align: center;">{{ $record->amount }}</td>
                    <td style="text-align: left; font-weight: bold;">
                        {{ $record->arrival_time }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-container">
        <table class="totals-table" align="left">
            <tr>
                <td style="color: #666;">عدد الأصناف:</td>
                <td style="text-align: left;">{{ $records->count() }} صنف</td>
            </tr>
            <tr class="total-row final">
                <td style="padding-top: 15px;">إجمالي الوحدات:</td>
                <td style="text-align: left; padding-top: 15px;">{{ number_format($totalAmount) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both; margin-top: 60px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
        <p style="color: #666; font-size: 12pt;">تم استخراج هذا المستند إلكترونياً لإدارة النقل الداخلي بين المستودعات.
        </p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>

</body>

</html>
