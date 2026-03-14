<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashierSale;
use App\Models\CompanySalesTransfer;
use App\Models\CompanyTreasure;
use App\Models\Order;
use App\Models\ProductImportItem;
use Mpdf\Mpdf;

class FinancialReportController extends Controller
{
  public function print(Request $request)
  {
    $from = $request->get('from');
    $to = $request->get('to');

    $importsQuery = ProductImportItem::with('productVariant');
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
      'transfers_out' => $details['transfers']->where('trans_type', 'withdrawal')->sum('quantity'),
    ];
    $totals['transfers_net'] = $totals['transfers_in'] - $totals['transfers_out'];
    $totals['net_profit'] = ($totals['cashier'] + $totals['online']) - $totals['imports'];

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
          'cairo' => [
            'R' => 'Cairo-Bold-2.ttf',
            'useOTL' => 0xFF,
          ]
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

      $html = view('reports.financial-pdf', compact('totals', 'details', 'from', 'to'))->render();

      $mpdf->WriteHTML($html);
      return $mpdf->Output("Detailed-Financial-Report.pdf", 'I');

    } catch (\Mpdf\MpdfException $e) {
      return $e->getMessage();
    }
  }
}
