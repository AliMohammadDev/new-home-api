<?php

namespace App\Filament\Exports;

use App\Models\CashierSale;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CashierSaleExporter extends Exporter
{
    protected static ?string $model = CashierSale::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('رقم تسلسلي'),
            ExportColumn::make('cashier_sales_fatora_id')->label('رقم معرف فاتورة المبيع'),
            ExportColumn::make('product_variant_id')->label('رقم معرف خيار المنتج'),
            ExportColumn::make('sales_point_cashier_id')->label('رقم معرف الموظف الكاشير'),
            ExportColumn::make('quantity')->label('الكمية'),
            ExportColumn::make('price')->label('السعر الإفرادي'),
            ExportColumn::make('full_price')->label('السعر الإجمالي'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your cashier sale export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
