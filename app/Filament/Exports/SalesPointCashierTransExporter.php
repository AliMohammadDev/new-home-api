<?php

namespace App\Filament\Exports;

use App\Models\SalesPointCashierTrans;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SalesPointCashierTransExporter extends Exporter
{
    protected static ?string $model = SalesPointCashierTrans::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('sales_point_id'),
            ExportColumn::make('sales_point_manager_id'),
            ExportColumn::make('sales_point_cashier_id'),
            ExportColumn::make('trans_type'),
            ExportColumn::make('name'),
            ExportColumn::make('date'),
            ExportColumn::make('amount'),
            ExportColumn::make('waste'),
            ExportColumn::make('note'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your sales point cashier trans export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
