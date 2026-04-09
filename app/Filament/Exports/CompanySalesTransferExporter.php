<?php

namespace App\Filament\Exports;

use App\Models\CompanySalesTransfer;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CompanySalesTransferExporter extends Exporter
{
    protected static ?string $model = CompanySalesTransfer::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('sales_point_id'),
            ExportColumn::make('trans_type'),
            ExportColumn::make('name'),
            ExportColumn::make('date'),
            ExportColumn::make('quantity'),
            ExportColumn::make('note'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your company sales transfer export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
