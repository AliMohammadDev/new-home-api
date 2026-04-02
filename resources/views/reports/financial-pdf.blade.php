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

        .totals-table {
            width: 450px;
            border-top: 2px solid #eee;
        }

        .total-row.final {
            background-color: #f8fcfb;
            color: #025043;
            font-weight: bold;
            font-size: 20pt;
        }

        .badge-income {
            color: #10b981;
            font-weight: bold;
        }

        .badge-expense {
            color: #ef4444;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td class="document-title" style="width: 60%; vertical-align: middle;">
                <h1>التقرير المالي العام الشامل</h1>
            </td>
            <td style="width: 40%; text-align: left; vertical-align: middle;">
                @php $logoPath = public_path('images/logo.png'); @endphp
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
                <h4 style="color: #888; margin-bottom: 5px; font-size: 11pt;">ملخص التقرير:</h4>
                <strong style="font-size: 16pt;">تحليل التدفقات النقدية</strong><br>
                <span>يشمل: المبيعات، الاستيراد، وتحويلات النقاط</span>
            </td>
            <td style="text-align: left; vertical-align: top;">
                <h4 style="color: #888; margin-bottom: 5px; font-size: 11pt;">بيانات المستند:</h4>
                <span>تاريخ التقرير: {{ now()->format('Y/m/d') }}</span><br>
                <span>الحالة: <span style="color: #025043; font-weight: bold;">مراجعة نهائية</span></span>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40%;">البند المالي / المصدر</th>
                <th style="width: 25%; text-align: center;">نوع العملية</th>
                <th style="width: 35%; text-align: left;">القيمة الإجمالية</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>إجمالي مبيعات الكاشير ونقاط البيع</strong></td>
                <td style="text-align: center;"><span class="badge-income">إيراد (+)</span></td>
                <td style="text-align: left; font-weight: bold;">{{ number_format($totals['cashier'], 2) }} $</td>
            </tr>

            <tr>
                <td><strong>إجمالي مبيعات المتجر الإلكتروني</strong></td>
                <td style="text-align: center;"><span class="badge-income">إيراد (+)</span></td>
                <td style="text-align: left; font-weight: bold;">{{ number_format($totals['online'], 2) }} $</td>
            </tr>

            <tr>
                <td><strong>تكاليف عمليات الاستيراد والمشتريات</strong></td>
                <td style="text-align: center;"><span class="badge-expense">مصاريف (-)</span></td>
                <td style="text-align: left; font-weight: bold; color: #ef4444;">
                    {{ number_format($totals['imports'], 2) }} $</td>
            </tr>

            <tr>
                <td>إيداعات نقاط البيع (Deposits)</td>
                <td style="text-align: center;">تحويل داخلي</td>
                <td style="text-align: left;">{{ number_format($totals['transfers_in'], 2) }} $</td>
            </tr>

            <tr>
                <td>سحوبات نقاط البيع (withdraw)</td>
                <td style="text-align: center;">تحويل داخلي</td>
                <td style="text-align: left;">{{ number_format($totals['transfers_out'], 2) }} $</td>
            </tr>
        </tbody>
    </table>

    <table class="totals-table" align="left">
        <tr>
            <td style="color: #666; padding: 5px 0;">صافي العمليات (المبيعات - المشتريات):</td>
            <td style="text-align: left; font-weight: bold;">{{ number_format($totals['net_profit'], 2) }} $</td>
        </tr>
        <tr>
            <td style="color: #666; padding: 5px 0;">صافي تحويلات نقاط البيع:</td>
            <td style="text-align: left; font-weight: bold;">{{ number_format($totals['transfers_net'], 2) }} $</td>
        </tr>
        <tr class="total-row final">
            <td style="padding-top: 15px;">رصيد الخزينة الحالي:</td>
            <td style="text-align: left; padding-top: 15px;">{{ number_format($totals['treasure'], 2) }} $</td>
        </tr>
    </table>

    <div style="clear: both; margin-top: 80px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
        <p style="color: #888; font-size: 10pt;">هذا المستند سري ويستخدم للأغراض الإدارية فقط - نظام المحاسبة المركزي
        </p>
    </div>




    <div style="margin-top: 50px;">
        <h3 style="color: #025043; border-bottom: 2px solid #025043; padding-bottom: 5px;">تفاصيل حركات رأس المال
            (الاستيراد)</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th style="text-align: center;">الكمية</th>
                    <th style="text-align: left;">التكلفة الإجمالية</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details['imports'] as $import)
                    <tr>
                        <td>
                            {{ $import->productVariant->product->name['ar'] ?? 'منتج غير معرف' }}
                        </td>
                        <td style="text-align: center;">{{ $import->quantity }}</td>
                        <td style="text-align: left;" class="badge-expense">{{ number_format($import->total_cost, 2) }}
                            $</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h3 style="color: #025043; border-bottom: 2px solid #025043; padding-bottom: 5px;">تفاصيل مبيعات الموظفين
            (الكاشير)</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>اسم الموظف (User)</th>
                    <th>المنتج المباع</th>
                    <th style="text-align: center;">الكمية</th>
                    <th style="text-align: left;">المبلغ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details['cashier'] as $sale)
                    <tr>
                        <td><strong>{{ $sale->cashier->user->name ?? 'غير معروف' }}</strong></td>
                        <td>{{ $sale->variant->product->name['ar'] ?? 'منتج' }}</td>
                        <td style="text-align: center;">{{ $sale->quantity }}</td>
                        <td style="text-align: left;" class="badge-income">{{ number_format($sale->full_price, 2) }} $
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h3 style="color: #025043; border-bottom: 2px solid #025043; padding-bottom: 5px;">تفاصيل مبيعات المتجر
            الإلكتروني</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>العميل</th>
                    <th>التاريخ</th>
                    <th style="text-align: left;">القيمة</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details['online'] as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user->name ?? 'زائر' }}</td>
                        <td>{{ $order->created_at->format('Y/m/d') }}</td>
                        <td style="text-align: left;" class="badge-income">{{ number_format($order->total_amount, 2) }}
                            $</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h3 style="color: #025043; border-bottom: 2px solid #025043; padding-bottom: 5px;">تفاصيل حركات نقاط البيع</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40%;">نوع الحركة</th>
                    <th style="width: 25%; text-align: center;">التاريخ</th>
                    <th style="width: 35%; text-align: left;">القيمة</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details['transfers'] as $trans)
                    <tr>
                        <td>{{ $trans->trans_type == 'deposit' ? 'إيداع إلى الخزينة (+)' : 'سحب من الخزينة (-)' }}</td>
                        <td style="text-align: center;">{{ $trans->created_at->format('Y/m/d') }}</td>
                        <td style="text-align: left; font-weight: bold;">{{ number_format($trans->quantity, 2) }} $
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>



</body>

</body>

</html>
