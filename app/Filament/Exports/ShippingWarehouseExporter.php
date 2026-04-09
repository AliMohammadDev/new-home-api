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
            ExportColumn::make('id'),
            ExportColumn::make('product_variant_id'),
            ExportColumn::make('user_id'),
            ExportColumn::make('warehouse_id'),
            ExportColumn::make('arrival_time'),
            ExportColumn::make('amount'),
            ExportColumn::make('unit_name'),
            ExportColumn::make('unit_capacity'),
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
