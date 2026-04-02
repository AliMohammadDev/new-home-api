<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashierSale;
use App\Models\CompanySalesTransfer;
use App\Models\CompanyTreasure;
use App\Models\Order;
use App\Models\ProductImportItem;
use App\Models\ProductVariant;
use App\Models\ShippingWarehouse;
use App\Models\Warehouse;
use App\Models\WarehouseReturn;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class FinancialReportController extends Controller
{
  public function print(Request $request)
  {
    $from = $request->get('from');
    $to = $request->get('to');

    $importsQuery = ProductImportItem::with('productVariant.product');
    $cashierQuery = CashierSale::with(['cashier.user', 'variant.product']);
    $onlineQuery = Order::where('status', 'completed');
    $transfersQuery = CompanySalesTransfer::query();

    if ($from && $to) {
      $range = [$from . ' 00:00:00', $to . ' 23:59:59'];
      $importsQuery->whereBetween('created_at', $range);
      $cashierQuery->whereBetween('created_at', $range);
      $onlineQuery->whereBetween('created_at', $range);
      $transfersQuery->whereBetween('created_at', $range);
    }

    $details = [
      'imports' => $importsQuery->latest()->get(),
      'cashier' => $cashierQuery->latest()->get(),
      'online' => $onlineQuery->latest()->get(),
      'transfers' => $transfersQuery->latest()->get(),
    ];

    $totals = [
      'imports' => $details['imports']->sum('total_cost'),
      'cashier' => $details['cashier']->sum('full_price'),
      'online' => $details['online']->sum('total_amount'),
      'treasure' => CompanyTreasure::sum('money'),
      'transfers_in' => $details['transfers']->where('trans_type', 'deposit')->sum('quantity'),
      'transfers_out' => $details['transfers']->where('trans_type', 'withdraw')->sum('quantity'),
    ];
    $totals['transfers_net'] = $totals['transfers_in'] - $totals['transfers_out'];
    $totals['net_profit'] = ($totals['cashier'] + $totals['online']) - $totals['imports'];

    return $this->generatePdf('reports.financial-pdf', compact('totals', 'details', 'from', 'to'), "Financial-Report.pdf");
  }

  public function printProductReport(Request $request)
  {
    $from = $request->get('from');
    $to = $request->get('to');
    $range = [$from . ' 00:00:00', $to . ' 23:59:59'];

    $wasteWarehouse = Warehouse::where('name', 'like', '%هدر%')->first();

    $details = [

      'main_stock_summary' => ProductVariant::where('stock_quantity', '>', 0)
        ->selectRaw('SUM(stock_quantity) as total_qty')
        ->first(),

      'sub_warehouses_summary' => ShippingWarehouse::with('warehouse')
        ->selectRaw('warehouse_id, SUM(amount) as total_amount')
        ->groupBy('warehouse_id')
        ->get(),


      'waste_daily' => $wasteWarehouse ?
        DB::table('shipping_warehouses')
          ->where('warehouse_id', $wasteWarehouse->id)
          ->whereBetween('arrival_time', $range)
          ->selectRaw('DATE(arrival_time) as date, SUM(amount) as total_qty')
          ->groupBy('date')
          ->orderBy('date', 'desc')
          ->get() : collect([]),

      'imports' => ProductImportItem::whereBetween('created_at', $range)
        ->selectRaw('DATE(created_at) as date, SUM(quantity) as total_qty, SUM(total_cost) as total_price')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get(),

      'sales' => CashierSale::whereBetween('created_at', $range)
        ->selectRaw('DATE(created_at) as date, SUM(quantity) as total_qty')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get(),

      'returns' => WarehouseReturn::whereBetween('created_at', $range)
        ->selectRaw('DATE(created_at) as date, SUM(amount) as total_amount')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get(),

      'waste_details' => $wasteWarehouse ? $wasteWarehouse->productVariants : collect([]),

      'main_stock_details' => ProductVariant::with('product')
        ->where('stock_quantity', '>', 0)
        ->get(),

      'warehouses_stock' => ShippingWarehouse::sum('amount'),
    ];

    $totals = [
      'main_stock' => ProductVariant::sum('stock_quantity'),
      'sub_warehouses_stock' => $details['warehouses_stock'],
      'total_imported' => $details['imports']->sum('total_qty'),
      'sold_items' => $details['sales']->sum('total_qty'),
      'returned_items' => $details['returns']->sum('total_amount'),
      'wasted_items' => $details['waste_details']->sum(function ($item) {
        return $item->pivot->amount ?? 0;
      }),
    ];

    return $this->generatePdf('reports.product-pdf', compact('totals', 'details', 'from', 'to'), "Daily-Inventory-Report-$from.pdf");
  }
  private function generatePdf($view, $data, $filename)
  {
    if (ob_get_contents())
      ob_end_clean();

    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];
    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    try {
      $mpdf = new Mpdf([
        'fontDir' => array_merge($fontDirs, [storage_path('fonts')]),
        'fontdata' => $fontData + [
          'cairo' => ['R' => 'Cairo-Bold-2.ttf', 'useOTL' => 0xFF]
        ],
        'default_font' => 'cairo',
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 10,
        'margin_bottom' => 10,
      ]);

      $mpdf->SetDirectionality('rtl');
      $html = view($view, $data)->render();
      $mpdf->WriteHTML($html);
      return $mpdf->Output($filename, 'I');

    } catch (\Mpdf\MpdfException $e) {
      return $e->getMessage();
    }
  }
}
