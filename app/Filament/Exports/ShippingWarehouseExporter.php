<?php

namespace App\Filament\Exports;

use App\Models\ShippingWarehouse;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ShippingWarehouseExporter extends Exporter
{
    protected static ?string $model = ShippingWarehouse::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('رقم تسلسلي'),
            ExportColumn::make('product_variant_id')->label('رقم معرف خيار المنتج'),
            ExportColumn::make('user_id')->label('رقم معرف المستخدم'),
            ExportColumn::make('warehouse_id')->label('رقم معرف المستودع'),
            ExportColumn::make('arrival_time')->label('الوقت المستهدف'),
            ExportColumn::make('amount')->label('الكمية'),
            ExportColumn::make('unit_name')->label('اسم الوحدة'),
            ExportColumn::make('unit_capacity')->label('سعة الوحدة'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your shipping warehouse export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
