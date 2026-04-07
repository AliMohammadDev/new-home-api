<?php

namespace App\Filament\Pages;

use App\Models\ProductVariant;
use App\Models\ShippingWarehouse;
use App\Models\ProductImportItem;
use App\Models\CashierSale;
use App\Models\OrderItem;
use App\Models\Warehouse;
use App\Models\WarehouseReturn;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class ProductReports extends Page implements HasForms
{
  use InteractsWithForms;
  use HasPageShield;

  protected static string $view = 'filament.pages.product-reports';
  protected static ?string $navigationIcon = 'heroicon-o-beaker';
  protected static ?string $navigationGroup = 'التقارير والإحصائيات';
  protected static ?string $navigationLabel = 'تقارير المواد والمخزون';
  protected static ?string $title = 'تقرير جرد المواد التفصيلي';

  public ?array $data = [];
  public array $totals = [];

  public function mount(): void
  {
    $this->form->fill([
      'from_date' => now()->startOfMonth()->format('Y-m-d'),
      'to_date' => now()->format('Y-m-d'),
    ]);
    $this->calculateTotals();
  }

  public function form(Form $form): Form
  {
    return $form->schema([
      DatePicker::make('from_date')->label('من تاريخ')->required(),
      DatePicker::make('to_date')->label('إلى تاريخ')->required(),
    ])->columns(2)->statePath('data');
  }

  public function filter()
  {
    $this->calculateTotals();
  }

  protected function calculateTotals()
  {
    $from = $this->data['from_date'] . ' 00:00:00';
    $to = $this->data['to_date'] . ' 23:59:59';

    $wasteWarehouse = Warehouse::where('name', 'like', '%هدر%')->first();


    $cashierSold = CashierSale::whereBetween('created_at', [$from, $to])->sum('quantity');

    $onlineSold = OrderItem::whereHas('order', function ($query) use ($from, $to) {
      $query->where('status', 'completed')
        ->whereBetween('created_at', [$from, $to]);
    })->sum('quantity');


    $this->totals = [
      'main_stock' => ProductVariant::sum('stock_quantity'),
      'sub_warehouses_stock' => ShippingWarehouse::sum('amount'),
      'total_imported' => ProductImportItem::whereBetween('created_at', [$from, $to])->sum('quantity'),
      'wasted_items' => $wasteWarehouse
        ? $wasteWarehouse->productVariants()
          ->wherePivotBetween('created_at', [$from, $to])
          ->sum('amount')
        : 0,
      'sold_items' => $cashierSold + $onlineSold,
      'cashier_sold' => $cashierSold,
      'online_sold' => $onlineSold,
      'returned_items' => WarehouseReturn::whereBetween('created_at', [$from, $to])->sum('amount'),
    ];
  }


}