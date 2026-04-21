<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OrderExporter extends Exporter
{
  protected static ?string $model = Order::class;

  public static function getColumns(): array
  {
    return [
      ExportColumn::make('id')->label('رقم تسلسلي'),
      ExportColumn::make('user_id')->label('رقم معرف المستخدم'),
      ExportColumn::make('cart_id')->label('رقم معرف سلةالتسوق'),
      ExportColumn::make('checkout_id')->label('رقم معرف عملية الدفع'),
      ExportColumn::make('total_amount')->label('المبلغ الإجمالي'),
      ExportColumn::make('delivery_company_id')->label('رقم معرف شركة التوصيل'),
      ExportColumn::make('shipping_fee')->label('ضريبة الشحن'),
      ExportColumn::make('delivery_fee')->label('ضريبة التوصيل'),
      ExportColumn::make('payment_method')->label('طريقة الدفع'),
      ExportColumn::make('status')->label('حالة الطلب'),
    ];
  }

  public static function getCompletedNotificationBody(Export $export): string
  {
    $body = 'Your order export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

    if ($failedRowsCount = $export->getFailedRowsCount()) {
      $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
    }

    return $body;
  }
}