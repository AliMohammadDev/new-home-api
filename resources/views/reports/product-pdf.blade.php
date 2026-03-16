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
            font-size: 28pt;
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
            font-size: 14pt;
        }

        .items-table td {
            padding: 15px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 13pt;
        }

        .badge-income {
            color: #10b981;
            font-weight: bold;
        }

        .badge-expense {
            color: #ef4444;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td class="document-title" style="width: 60%; vertical-align: middle;">
                <h1>تقرير حركة المخزون التجميعي</h1>
            </td>
            <td style="width: 40%; text-align: left; vertical-align: middle;">
                @php $logoPath = public_path('images/logo-no-black.png'); @endphp
                @if (file_exists($logoPath))
                    <img src="{{ $logoPath }}" width="130">
                @else
                    <h2 style="color: #025043;">{{ config('app.name') }}</h2>
                @endif
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-bottom: 30px;">
        <tr>
            <td style="text-align: right; width: 50%;">
                <h4 style="color: #888; margin-bottom: 5px; font-size: 11pt;">ملخص حركة الفترة:</h4>
                <strong style="font-size: 16pt;">تحليل البيانات اللوجستية</strong><br>
                <span>تجميع يومي للواردات، المبيعات، والمرتجعات</span>
            </td>
            <td style="text-align: left; vertical-align: top;">
                <h4 style="color: #888; margin-bottom: 5px; font-size: 11pt;">بيانات المستند:</h4>
                <span>الفترة: من {{ $from }} إلى {{ $to }}</span><br>
                <span>تاريخ الاستخراج: {{ now()->format('Y/m/d') }}</span>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40%;">البند الإحصائي</th>
                <th style="width: 25%; text-align: center;">الحالة</th>
                <th style="width: 35%; text-align: left;">إجمالي الكمية (قطعة)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>إجمالي المستوردات (الداخل)</strong></td>
                <td style="text-align: center;"><span class="">توريد (+)</span></td>
                <td style="text-align: left; font-weight: bold;">{{ number_format($totals['total_imported']) }}</td>
            </tr>
            <tr>
                <td><strong>إجمالي المبيعات (الخارج)</strong></td>
                <td style="text-align: center;"><span class="">بيع (-)</span></td>
                <td style="text-align: left; font-weight: bold;"> {{ number_format($totals['sold_items']) }}</td>
            </tr>
            <tr>
                <td><strong>إجمالي المرتجعات</strong></td>
                <td style="text-align: center;">إعادة للمخزن</td>
                <td style="text-align: left;">{{ number_format($totals['returned_items']) }}</td>
            </tr>
            <tr style="background-color: #fcf8f8;">
                <td><strong>إجمالي المهدورات / التلف</strong></td>
                <td style="text-align: center;"><span class="">تلف</span></td>
                <td style="text-align: left; ">{{ number_format($totals['wasted_items']) }}</td>
            </tr>
        </tbody>
    </table>



    <div style="margin-top: 50px;">
        <h3 style="border-bottom: 2px solid #025043; padding-bottom: 5px;">حالة المخزون الرئيسي </h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 30%;">تاريخ الجرد</th>
                    <th style="width: 40%;">الموقع</th>
                    <th style="text-align: center;">إجمالي الكمية المتوفرة</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ now()->format('Y/m/d') }}</strong></td>
                    <td>المستودع الرئيسي المركزي</td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ number_format($details['main_stock_summary']->total_qty ?? 0) }} قطعة
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h3 style="border-bottom: 2px solid #025043; padding-bottom: 5px;">تفاصيل المستودعات المصغرة </h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 30%;">التاريخ</th>
                    <th style="width: 40%;">اسم المستودع الفرعي</th>
                    <th style="text-align: center;">إجمالي الكمية </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($details['sub_warehouses_summary'] as $sub)
                    <tr>
                        <td><strong>{{ now()->format('Y/m/d') }}</strong></td>
                        <td>{{ $sub->warehouse->name ?? 'مستودع فرعي' }}</td>
                        <td style="text-align: center;" class="">
                            {{ number_format($sub->total_amount) }} قطعة
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">لا توجد كميات في المستودعات المصغرة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 50px;">
        <h3 style="border-bottom: 2px solid #025043; padding-bottom: 5px;">تفاصيل الاستيراد </h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 30%;">تاريخ اليوم</th>
                    <th style="text-align: center;">إجمالي الكمية المستلمة</th>
                    <th style="text-align: left;">إجمالي تكلفة المشتريات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details['imports'] as $import)
                    <tr>
                        <td><strong>{{ $import->date }}</strong></td>
                        <td style="text-align: center;">{{ number_format($import->total_qty) }} قطعة</td>
                        <td style="text-align: left;">{{ number_format($import->total_price, 2) }} $</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h3 style="border-bottom: 2px solid #025043; padding-bottom: 5px;">تفاصيل المبيعات </h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 30%;">تاريخ اليوم</th>
                    <th style="text-align: center;">إجمالي عدد القطع المباعة</th>
                    <th style="text-align: left;">ملاحظات الحركة</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details['sales'] as $sale)
                    <tr>
                        <td><strong>{{ $sale->date }}</strong></td>
                        <td style="text-align: center;">{{ number_format($sale->total_qty) }} قطعة</td>
                        <td style="text-align: left;">مبيعات نقاط البيع المعتمدة</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h3 style="border-bottom: 2px solid #025043; padding-bottom: 5px;">تفاصيل المرتجعات </h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 30%;">التاريخ</th>
                    <th style="text-align: center;">إجمالي الكمية المرتجعة</th>
                    <th style="text-align: left;">حالة المرتجع</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($details['returns'] as $return)
                    <tr>
                        <td><strong>{{ $return->date }}</strong></td>
                        <td style="text-align: center;">{{ number_format($return->total_amount) }} قطعة</td>
                        <td style="text-align: left;">مرتجع لمستودع رئيسي</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">لا يوجد مرتجعات خلال هذه الفترة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h3 style="border-bottom: 2px solid padding-bottom: 5px;">تفاصيل المهدورات والتلفيات
            (تجميعي)</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 30%;">التاريخ</th>
                    <th style="text-align: center; ">إجمالي الكمية التالفة</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($details['waste_daily'] as $waste)
                    <tr>
                        <td><strong>{{ $waste->date }}</strong></td>
                        <td style="text-align: center; font-weight: bold;">
                            {{ number_format($waste->total_qty) }} قطعة
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">لا يوجد هدر مسجل خلال هذه الفترة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


</body>

</html>
