<?php

namespace App\Filament\Exports;

use App\Models\SupplierPayment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SupplierPaymentExporter extends Exporter
{
    protected static ?string $model = SupplierPayment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('رقم تسلسلي'),
            ExportColumn::make('product_import_item_id')->label('رقم معرف عملية الاستيراد'),
            ExportColumn::make('amount')->label('المبلغ'),
            ExportColumn::make('payment_date')->label('تاريخ الدفع'),
            ExportColumn::make('payment_method')->label('طريقة الدفع'),
            ExportColumn::make('trans_type')->label('نوع التحويل'),
            ExportColumn::make('notes')->label('ملاحظات'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your supplier payment export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
