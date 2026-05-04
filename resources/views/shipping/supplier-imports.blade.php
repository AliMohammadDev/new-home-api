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
            font-size: 11pt;
            background-color: #fff;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px solid #025043;
            margin-bottom: 25px;
            padding-bottom: 12px;
        }

        .document-title h1 {
            font-size: 26pt;
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
            padding: 10px 8px;
            text-align: center;
            font-size: 12pt;
            border: 1px solid #025043;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            border-left: 1px solid #f9f9f9;
            border-right: 1px solid #f9f9f9;
            font-size: 11pt;
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
            font-size: 11pt;
            border-bottom: 1px solid #f5f5f5;
        }

        .total-row.final {
            background-color: #025043;
            color: #ffffff;
            font-weight: bold;
            font-size: 16pt;
        }

        .total-row.final td {
            padding: 12px;
            border: none;
        }

        .text-muted {
            color: #666;
            font-size: 10pt;
        }

        .price-bold {
            font-weight: 700;
            color: #025043;
        }

        .footer-note {
            clear: both;
            margin-top: 50px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
            color: #888;
            font-size: 10pt;
        }

        small {
            font-size: 9pt;
            display: block;
            margin-top: 3px;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td class="document-title" style="width: 50%; vertical-align: middle;">
                <h1>بيان استلام بضائع</h1>
            </td>
            <td style="width: 50%; text-align: left; vertical-align: middle;">
                @php $logoPath = public_path('images/logo.png'); @endphp
                @if (file_exists($logoPath))
                    <img src="{{ $logoPath }}" width="130">
                @else
                    <h2 style="color: #025043; margin: 0; font-size: 20pt;">STORE NAME</h2>
                @endif
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td style="text-align: right; width: 50%; vertical-align: top;">
                <div style="border-right: 3px solid #eee; padding-right: 12px;">
                    <h4 class="text-muted" style="margin: 0 0 4px 0;">معلومات المورد:</h4>
                    <strong style="font-size: 15pt; color: #025043;">
                        {{ $records->first()->productImport->supplier_name ?? 'مورد عام' }}
                    </strong><br>
                    <span style="color: #444;">العنوان:
                        {{ $records->first()->productImport->address ?? 'غير محدد' }}</span>
                </div>
            </td>
            <td style="text-align: left; vertical-align: top; width: 50%;">
                <h4 class="text-muted" style="margin: 0 0 4px 0;">تفاصيل المستند:</h4>
                <span>تاريخ الاستخراج: <strong>{{ now()->format('Y/m/d') }}</strong></span><br>
                <span>الحالة: <span
                        style="color: #025043; font-weight: bold; background: #e6eeed; padding: 2px 8px; border-radius: 4px;">تم
                        الاستلام</span></span>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40%; text-align: right;">المنتج</th>
                <th>المواصفات</th>
                <th>الكمية</th>
                <th style="text-align: left;">سعر الوحدة</th>
            </tr>
        </thead>
        <tbody>
            @php $totalQuantity = 0; @endphp
            @foreach ($records as $record)
                @php $totalQuantity += $record->quantity; @endphp
                <tr>
                    <td style="text-align: right;">
                        <strong style="color: #025043; font-size: 11.5pt;">
                            {{ $record->productVariant->product->name['ar'] ?? ($record->productVariant->product->name['en'] ?? 'منتج غير معروف') }}
                        </strong>
                        <small class="text-muted">SKU: {{ $record->productVariant->sku }}</small>
                    </td>
                    <td>
                        {{ $record->productVariant->color->color['ar'] ?? '' }} /
                        {{ $record->productVariant->size->size ?? '' }}
                    </td>
                    <td style="font-weight: bold; font-size: 12pt;">{{ number_format($record->quantity) }}</td>
                    <td class="price-bold" style="text-align: left;">
                        ${{ number_format($record->price, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table" align="left">
        <tr>
            <td class="text-muted">عدد الأصناف المختلفة:</td>
            <td style="text-align: left; font-weight: 600;">{{ $records->count() }} صنف</td>
        </tr>
        <tr class="total-row ">
            <td>إجمالي كمية الشحنة:</td>
            <td style="text-align: left;">{{ number_format($totalQuantity) }} قطة</td>
        </tr>
    </table>



</body>

</html>
