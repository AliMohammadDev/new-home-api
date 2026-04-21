<?php

namespace App\Filament\Pages;

use App\Models\CashierSale;
use App\Models\CashierSalesReturn;
use App\Models\CompanySalesTransfer;
use App\Models\CompanyTreasure;
use App\Models\Order;
use App\Models\ProductImportItem;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class SalesReports extends Page implements HasForms
{
  use InteractsWithForms;
  use HasPageShield;

  protected static string $view = 'filament.pages.sales-reports';
  protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
  protected static ?string $navigationGroup = 'التقارير والإحصائيات';
  protected static ?string $navigationLabel = 'التقارير المالية';
  protected static ?string $title = 'التقرير المالي الشامل';

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

  protected function getFormStatePath(): string
  {
    return 'data';
  }

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        DatePicker::make('from_date')
          ->label('من تاريخ')
          ->required(),
        DatePicker::make('to_date')
          ->label('إلى تاريخ')
          ->required(),
      ])
      ->columns(2)
      ->statePath('data');
  }

  public function filter()
  {
    $this->calculateTotals();
  }

  protected function calculateTotals()
  {
    $from = $this->data['from_date'];
    $to = $this->data['to_date'];

    $importsQuery = ProductImportItem::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
    $cashierQuery = CashierSale::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
    $cashierReturnQuery = CashierSalesReturn::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
    $onlineQuery = Order::where('status', 'completed')->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
    $transInQuery = CompanySalesTransfer::where('trans_type', 'deposit')->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
    $transOutQuery = CompanySalesTransfer::where('trans_type', 'withdraw')->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

    $imports = $importsQuery->sum('total_cost');
    $cashier = $cashierQuery->sum('full_price');
    $cashier_return = $cashierReturnQuery->sum('full_price');
    $online = $onlineQuery->sum('total_amount');
    $trans_in = $transInQuery->sum('quantity');
    $trans_out = $transOutQuery->sum('quantity');

    $this->totals = [
      'imports' => $imports,
      'cashier' => $cashier,
      'cashier_return' => $cashier_return,
      'cashier_net' => $cashier - $cashier_return,
      'online' => $online,
      'treasure' => CompanyTreasure::sum('money'),
      'transfers_in' => $trans_in,
      'transfers_out' => $trans_out,
      'transfers_net' => $trans_in - $trans_out,
      'net_profit' => (($cashier - $cashier_return) + $online) - $imports,

      'count_imports' => $importsQuery->count(),
      'count_cashier' => $cashierQuery->count(),
      'count_returns' => $cashierReturnQuery->count(),
      'count_online' => $onlineQuery->count(),
      'count_trans_in' => $transInQuery->count(),
      'count_trans_out' => $transOutQuery->count(),
    ];
  }
}