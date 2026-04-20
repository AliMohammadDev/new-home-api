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
            font-size: 13pt;
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
            padding: 12px 8px;
            text-align: center;
            font-size: 14pt;
            border: 1px solid #025043;
        }

        .items-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #ddd;
            border-left: 1px solid #eee;
            border-right: 1px solid #eee;
            font-size: 13pt;
            text-align: center;
        }

        .totals-table {
            width: 380px;
            border-top: 2px solid #eee;
        }

        .total-row.final {
            background-color: #f8fcfb;
            color: #025043;
            font-weight: bold;
            font-size: 20pt;
        }

        .text-muted {
            color: #888;
            font-size: 11pt;
        }

        .text-danger {
            color: #a80000;
        }

        .price-bold {
            font-weight: bold;
            color: #025043;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td class="document-title" style="width: 50%; vertical-align: middle;">
                <h1>بيان تفصيلي للشحنة</h1>
            </td>
            <td style="width: 50%; text-align: left; vertical-align: middle;">
                @php $logoPath = public_path('images/logo.png'); @endphp
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
                <h4 class="text-muted" style="margin-bottom: 5px;">بيانات المورد والشحن:</h4>
                <strong
                    style="font-size: 18pt;">{{ $records->first()->productImport->supplier_name ?? 'مورد عام' }}</strong><br>
                <span>العنوان: {{ $records->first()->productImport->address ?? 'غير محدد' }}</span>
            </td>
            <td style="text-align: left; vertical-align: top;">
                <h4 class="text-muted" style="margin-bottom: 5px;">توثيق المستند:</h4>
                <span>تاريخ الاستخراج: {{ now()->format('Y/m/d') }}</span><br>
                <span>حالة القيد: <span style="color: #025043; font-weight: bold;">مكتمل (استيراد)</span></span>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30%; text-align: right;">المنتج</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>شحن/وحدة</th>
                <th>خصم صنف</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQty = 0;
                $sumBasePrice = 0;
                $sumShipping = 0;
                $sumDiscounts = 0;
                $finalGrandTotal = 0;
            @endphp
            @foreach ($records as $record)
                @php
                    $totalQty += $record->quantity;
                    $sumBasePrice += $record->price * $record->quantity;
                    $sumShipping += $record->shipping_price * $record->quantity;
                    $sumDiscounts += $record->discount;
                    $finalGrandTotal += $record->total_cost;
                @endphp
                <tr>
                    <td style="text-align: right;">
                        <strong
                            style="color: #025043;">{{ $record->productVariant->product->name['ar'] ?? ($record->productVariant->product->name['en'] ?? 'منتج') }}</strong>
                        <br>
                        <small class="text-muted">{{ $record->productVariant->color->color ?? '' }} /
                            {{ $record->productVariant->size->size ?? '' }} ({{ $record->productVariant->sku }})</small>
                    </td>
                    <td style="font-weight: bold;">{{ number_format($record->quantity) }}</td>
                    <td>${{ number_format($record->price, 2) }}</td>
                    <td>${{ number_format($record->shipping_price, 2) }}</td>
                    <td class="text-danger">-${{ number_format($record->discount, 2) }}</td>
                    <td class="price-bold">${{ number_format($record->total_cost, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table" align="left">
        <tr>
            <td class="text-muted">إجمالي قيمة المنتجات:</td>
            <td style="text-align: left;">${{ number_format($sumBasePrice, 2) }}</td>
        </tr>
        <tr>
            <td class="text-muted">إجمالي تكاليف الشحن:</td>
            <td style="text-align: left;">${{ number_format($sumShipping, 2) }}</td>
        </tr>
        <tr>
            <td class="text-muted">إجمالي الخصومات الممنوحة:</td>
            <td style="text-align: left;" class="text-danger">-${{ number_format($sumDiscounts, 2) }}</td>
        </tr>
        <tr>
            <td class="text-muted">إجمالي عدد القطع:</td>
            <td style="text-align: left;">{{ number_format($totalQty) }} قطعة</td>
        </tr>
        <tr class="total-row final">
            <td style="padding-top: 15px;">الصافي النهائي:</td>
            <td style="text-align: left; padding-top: 15px;">${{ number_format($finalGrandTotal, 2) }}</td>
        </tr>
    </table>

    <div style="clear: both; margin-top: 60px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
        <p class="text-muted">تم استخراج هذا البيان آلياً لإدارة شؤون الاستيراد والمستودعات.</p>
    </div>

</body>

</html>
