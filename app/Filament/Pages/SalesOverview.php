<?php

namespace App\Filament\Pages;

use App\Models\CashierSale;
use App\Models\Product;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class SalesOverview extends Page implements HasTable
{
  use InteractsWithTable;
  use HasPageShield;
  protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
  protected static ?string $navigationGroup = 'التقارير والإحصائيات';
  protected static ?string $navigationLabel = 'ملخص المبيعات العام';
  protected static ?string $title = 'تقرير حركة مبيعات المواد';

  protected static string $view = 'filament.pages.sales-overview';

  public function table(Table $table): Table
  {
    return $table
      ->query(CashierSale::query()->with(['variant.product', 'cashier.salesPoint']))
      ->columns([
        Tables\Columns\TextColumn::make('variant.product.name')
          ->label('المنتج')
          ->formatStateUsing(fn($record) => $record->variant?->product?->translated_name ?? 'بدون اسم')

          ->searchable(query: function (Builder $query, string $search): Builder {
            return $query->whereHas('variant', function ($q) use ($search) {
              $q->where('sku', 'like', "%{$search}%")
                ->orWhereHas('product', function ($productQuery) use ($search) {
                  $productQuery->where('name->ar', 'like', "%{$search}%")
                    ->orWhere('name->en', 'like', "%{$search}%");
                });
            });
          })

          ->weight('bold')
          ->description(fn($record) => " SKU: " . $record->variant?->sku)
          ->color('primary'),
        Tables\Columns\TextColumn::make('variant.color.color')
          ->label('اللون / المقاس')
          ->formatStateUsing(fn($record) => ($record->variant->color?->color ?? '-') . ' / ' . ($record->variant->size?->size ?? '-'))
          ->iconColor(fn($record) => $record->variant->color?->color ?? 'gray')
          ->badge()
          ->color('gray'),
        Tables\Columns\TextColumn::make('cashier.salesPoint.name')
          ->label('نقطة البيع')
          ->badge()
          ->color('info')
          ->sortable(),

        Tables\Columns\TextColumn::make('quantity')
          ->label('الكمية')
          ->numeric(
            decimalPlaces: 0,
            locale: 'en',
          )
          ->alignCenter()
          ->weight('bold')
          ->iconColor('warning')
          ->summarize(
            Tables\Columns\Summarizers\Sum::make()
              ->label('الإجمالي')
              ->numeric(locale: 'en')
          ),

        Tables\Columns\TextColumn::make('full_price')
          ->label('القيمة الإجمالية')
          ->money('USD', locale: 'en_US')
          ->color('success')
          ->weight('bold')
          ->sortable()
          ->summarize(
            Tables\Columns\Summarizers\Sum::make()
              ->label('مجموع المبيعات')
              ->money('USD', locale: 'en_US')
          ),

        Tables\Columns\TextColumn::make('created_at')
          ->label('وقت البيع')
          ->dateTime('d/M H:i')
          ->description(fn($record) => $record->created_at->diffForHumans())
          ->sortable()
          ->color('gray'),
      ])
      ->filters([
        SelectFilter::make('sales_point')
          ->label('نقطة البيع')
          ->relationship('cashier.salesPoint', 'name'),

        SelectFilter::make('product_id')
          ->label('المنتج المبيع')
          ->options(
            Product::all()
              ->pluck('translated_name', 'id')
              ->toArray()
          )
          ->query(function (Builder $query, array $data) {
            if ($data['value']) {
              $query->whereHas('variant', function ($q) use ($data) {
                $q->where('product_id', $data['value']);
              });
            }
          })
          ->searchable()
          ->preload(),

        Filter::make('created_at')
          ->form([
            DatePicker::make('from')->label('من تاريخ'),
            DatePicker::make('until')->label('إلى تاريخ'),
          ])
          ->query(
            fn(Builder $query, array $data): Builder => $query
              ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
              ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']))
          )
      ])
      ->defaultSort('created_at', 'desc')
      ->groups([
        Tables\Grouping\Group::make('cashier.salesPoint.name')
          ->label('نقطة البيع')
          ->collapsible(),
      ]);
  }
}
