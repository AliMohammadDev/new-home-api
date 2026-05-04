<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'cairo', 'Cairo', sans-serif !important;
        }

        body {
            direction: rtl;
            color: #222;
            line-height: 1.5;
            margin: 0;
            padding: 25px;
            font-size: 12pt;
            background-color: #fff;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px solid #025043;
            margin-bottom: 25px;
            padding-bottom: 12px;
        }

        .document-title h1 {
            font-size: 28pt;
            margin: 0;
            color: #025043;
            text-align: right;
            font-weight: 700;
        }

        .info-table {
            width: 100%;
            margin-bottom: 35px;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 35px;
        }

        .items-table th {
            background-color: #025043;
            color: #ffffff;
            padding: 12px 8px;
            text-align: center;
            font-size: 13pt;
            border: 1px solid #025043;
        }

        .items-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #eee;
            border-left: 1px solid #fcfcfc;
            border-right: 1px solid #fcfcfc;
            font-size: 11.5pt;
            text-align: center;
            vertical-align: middle;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #fbfdfc;
        }

        .totals-table {
            width: 380px;
            float: left;
            border-spacing: 0;
        }

        .totals-table td {
            padding: 8px 12px;
            font-size: 12pt;
            border-bottom: 1px solid #f5f5f5;
        }

        .total-row.final {
            background-color: #025043;
            color: #ffffff;
            font-weight: bold;
            font-size: 18pt;
        }

        .total-row.final td {
            padding: 12px;
            border: none;
        }

        .text-muted {
            color: #666;
            font-size: 11pt;
        }



        .price-bold {
            font-weight: 700;
            color: #025043;
        }

        small {
            font-size: 10pt;
            display: block;
            margin-top: 3px;
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
                    <img src="{{ $logoPath }}" width="140">
                @else
                    <h2 style="color: #025043; margin: 0; font-size: 22pt;">STORE NAME</h2>
                @endif
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td style="text-align: right; width: 50%; vertical-align: top;">
                <div style="border-right: 3px solid #eee; padding-right: 12px;">
                    <h4 class="text-muted" style="margin: 0 0 4px 0;">بيانات المورد والشحن:</h4>
                    <strong style="font-size: 16pt; color: #025043;">
                        {{ $records->first()->productImport->supplier_name ?? 'مورد عام' }}
                    </strong><br>
                    <span style="color: #444; font-size: 12pt;">العنوان:
                        {{ $records->first()->productImport->address ?? 'غير محدد' }}</span>
                </div>
            </td>
            <td style="text-align: left; vertical-align: top; width: 50%;">
                <h4 class="text-muted" style="margin: 0 0 4px 0;">توثيق المستند:</h4>
                <span style="font-size: 12pt;">تاريخ الاستخراج: <strong>{{ now()->format('Y/m/d') }}</strong></span><br>

            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 35%; text-align: right;">المنتج والتفاصيل</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>الشحن</th>
                <th>الخصم</th>
                <th style="text-align: left;">الإجمالي</th>
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
                        <strong style="color: #025043; font-size: 12pt;">
                            {{ $record->productVariant->product->name['ar'] ?? ($record->productVariant->product->name['en'] ?? 'منتج') }}
                        </strong>
                        <small class="text-muted">
                            {{ $record->productVariant->color->color['ar'] ?? '' }} /
                            {{ $record->productVariant->size->size ?? '' }} ({{ $record->productVariant->sku }})
                        </small>
                    </td>
                    <td style="font-weight: bold; font-size: 12pt;">{{ number_format($record->quantity) }}</td>
                    <td>${{ number_format($record->price, 2) }}</td>
                    <td>${{ number_format($record->shipping_price, 2) }}</td>
                    <td class="text-danger">${{ number_format($record->discount, 2) }}</td>
                    <td class="price-bold" style="text-align: left; font-size: 12pt;">
                        ${{ number_format($record->total_cost, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table" align="left">
        <tr>
            <td class="text-muted">إجمالي قيمة المنتجات:</td>
            <td style="text-align: left; font-weight: 600;">${{ number_format($sumBasePrice, 2) }}</td>
        </tr>
        <tr>
            <td class="text-muted">إجمالي تكاليف الشحن:</td>
            <td style="text-align: left; font-weight: 600;">${{ number_format($sumShipping, 2) }}</td>
        </tr>
        <tr>
            <td class="text-muted">إجمالي الخصومات:</td>
            <td style="text-align: left;" class="text-danger">${{ number_format($sumDiscounts, 2) }}</td>
        </tr>
        <tr>
            <td class="text-muted">إجمالي الكمية:</td>
            <td style="text-align: left; font-weight: 600;">{{ number_format($totalQty) }} قطعة</td>
        </tr>
        <tr class="total-row ">
            <td>الصافي النهائي:</td>
            <td style="text-align: left;">${{ number_format($finalGrandTotal, 2) }}</td>
        </tr>
    </table>

</body>

</html>
