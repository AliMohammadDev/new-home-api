<?php

namespace App\Filament\Exports;

use App\Models\CashierSalesReturn;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CashierSalesReturnExporter extends Exporter
{
    protected static ?string $model = CashierSalesReturn::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('cashier_return_fatora_id'),
            ExportColumn::make('product_variant_id'),
            ExportColumn::make('sales_point_cashier_id'),
            ExportColumn::make('quantity'),
            ExportColumn::make('price'),
            ExportColumn::make('full_price'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your cashier sales return export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
