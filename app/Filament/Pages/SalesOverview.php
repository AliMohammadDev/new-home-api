<?php

namespace App\Filament\Pages;

use App\Models\ProductVariant;
use App\Models\SalesPoint;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class SalesOverview extends Page implements HasTable
{
  use InteractsWithTable;
  use HasPageShield;

  protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
  protected static ?string $navigationGroup = 'التقارير والإحصائيات';
  protected static ?string $navigationLabel = 'ملخص المبيعات العام';
  protected static ?string $title = 'تقرير حركة مبيعات المواد';

  protected static string $view = 'filament.pages.sales-overview';

  public ?string $activeTab = 'all';

  public function table(Table $table): Table
  {
    return $table
      ->query(function () {
        $cashierSales = DB::table('cashier_sales')
          ->join('sales_point_cashiers', 'cashier_sales.sales_point_cashier_id', '=', 'sales_point_cashiers.id')
          ->join('sales_points', 'sales_point_cashiers.sales_point_id', '=', 'sales_points.id')
          ->select([
            'cashier_sales.product_variant_id',
            'cashier_sales.quantity as sale_qty',
            'cashier_sales.full_price as sale_price',
            'cashier_sales.created_at as sale_date',
            'sales_points.name as sales_point_name',
            'sales_points.id as sales_point_id',
            DB::raw("'cashier' as source")
          ]);

        $onlineSales = DB::table('order_items')
          ->join('orders', 'order_items.order_id', '=', 'orders.id')
          ->where('orders.status', 'completed')
          ->whereNull('orders.deleted_at')
          ->select([
            'order_items.product_variant_id',
            'order_items.quantity as sale_qty',
            'order_items.total as sale_price',
            'order_items.created_at as sale_date',
            DB::raw("'المتجر الإلكتروني' as sales_point_name"),
            DB::raw("0 as sales_point_id"),
            DB::raw("'online' as source")
          ]);

        $unionQuery = $cashierSales->unionAll($onlineSales);

        return ProductVariant::query()
          ->joinSub($unionQuery, 'combined_sales', function ($join) {
            $join->on('product_variants.id', '=', 'combined_sales.product_variant_id');
          })
          ->with(['product', 'color', 'size', 'images'])
          ->select([
            'product_variants.*',
            'combined_sales.sale_qty',
            'combined_sales.sale_price',
            'combined_sales.sale_date',
            'combined_sales.sales_point_name',
            'combined_sales.sales_point_id',
            'combined_sales.source',
          ]);
      })
      ->columns([
        Tables\Columns\ImageColumn::make('variant_images')
          ->label('صور المنتج')
          ->circular()
          ->stacked()
          ->getStateUsing(function ($record) {
            if (!$record->images || $record->images->isEmpty()) {
              return null;
            }
            return $record->images->map(function ($img) use ($record) {
              return str_contains($img->image, 'product_variants/')
                ? $img->image
                : "product_variants/{$record->id}/{$img->image}";
            })->toArray();
          })
          ->disk('public'),

        Tables\Columns\TextColumn::make('product.name')
          ->label('المنتج')
          ->formatStateUsing(fn($record) => $record->product?->translated_name ?? 'بدون اسم')
          ->description(fn($record) => "SKU: {$record->sku}")
          ->searchable(),

        Tables\Columns\TextColumn::make('barcode')
          ->label('الباركود')
          ->formatStateUsing(fn($state) => $state ? new HtmlString(
            "<div class='flex flex-col items-center justify-center gap-1'>" .
            \DNS1D::getBarcodeHTML((string) $state, 'C128', 1.2, 22) .
            "<span class='text-[10px] font-mono'>$state</span></div>"
          ) : '-')
          ->html()
          ->alignCenter(),

        Tables\Columns\TextColumn::make('sales_point_name')
          ->label('نقطة البيع')
          ->badge()
          ->color(fn($state) => $state === 'المتجر الإلكتروني' ? 'success' : 'info')
          ->sortable(),

        Tables\Columns\TextColumn::make('specs')
          ->label('المواصفات')
          ->getStateUsing(fn($record) => "{$record->color?->color} / {$record->size?->size}")
          ->badge()
          ->color('gray'),

        Tables\Columns\TextColumn::make('sale_qty')
          ->label('الكمية')
          ->numeric(locale: 'en')
          ->alignCenter()
          ->summarize(Tables\Columns\Summarizers\Sum::make()->label('إجمالي')->numeric(locale: 'en')),

        Tables\Columns\TextColumn::make('sale_price')
          ->label('المبلغ')
          ->money('USD', locale: 'en')
          ->color('success')
          ->weight('bold')
          ->summarize(
            Tables\Columns\Summarizers\Sum::make()
              ->label('المجموع')
              ->numeric(locale: 'en')
              ->money('USD', locale: 'en')
          ),

        Tables\Columns\TextColumn::make('sale_date')
          ->label('التاريخ')
          ->dateTime('d/m/Y H:i')
          ->sortable(),
      ])
      ->filters([
        SelectFilter::make('sales_point_id')
          ->label('تصفية حسب نقطة البيع')
          ->options(function () {
            $points = SalesPoint::pluck('name', 'id')->toArray();
            return [0 => 'المتجر الإلكتروني'] + $points;
          })
          ->query(
            fn(Builder $query, array $data) => $query
              ->when($data['value'] !== null, fn($q) => $q->where('combined_sales.sales_point_id', $data['value']))
          ),

        Tables\Filters\Filter::make('sale_date')
          ->form([
            DatePicker::make('from')->label('من تاريخ'),
            DatePicker::make('until')->label('إلى تاريخ'),
          ])
          ->query(
            fn(Builder $query, array $data) => $query
              ->when($data['from'], fn($q) => $q->whereDate('combined_sales.sale_date', '>=', $data['from']))
              ->when($data['until'], fn($q) => $q->whereDate('combined_sales.sale_date', '<=', $data['until']))
          )
      ])
      ->groups([
        Tables\Grouping\Group::make('sales_point_name')
          ->label('نقطة البيع')
          ->collapsible(),
      ])
      ->modifyQueryUsing(function (Builder $query) {
        return match ($this->activeTab) {
          'cashier' => $query->where('source', 'cashier'),
          'online' => $query->where('source', 'online'),
          default => $query,
        };
      })
      ->defaultSort('sale_date', 'desc');
  }

  public function getTabs(): array
  {
    return [
      'all' => 'الكل',
      'cashier' => 'مبيعات الكاشير',
      'online' => 'مبيعات الموقع',
    ];
  }
}