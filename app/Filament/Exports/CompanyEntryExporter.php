<?php

namespace App\Filament\Exports;

use App\Models\CompanyEntry;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CompanyEntryExporter extends Exporter
{
  protected static ?string $model = CompanyEntry::class;

  public static function getColumns(): array
  {
    return [
      ExportColumn::make('id')->label('رقم تسلسلي'),
      ExportColumn::make('treasure.name')->label('اسم الصندوق'),
      ExportColumn::make('user.name')->label('اسم المستخدم'),
      ExportColumn::make('trans_type')
        ->label('نوع التحويل')
        ->formatStateUsing(fn(string $state): string => match ($state) {
          'deposit' => 'دائن',
          'withdraw' => 'مدين',
          default => $state,
        }),
      ExportColumn::make('name')->label('اسم البيان'),
      ExportColumn::make('amount')->label('المبلغ'),
    ];
  }

  public static function getCompletedNotificationBody(Export $export): string
  {
    $body = 'Your company entry export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

    if ($failedRowsCount = $export->getFailedRowsCount()) {
      $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
    }

    return $body;
  }
}
