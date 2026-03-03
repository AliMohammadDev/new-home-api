<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashierSalesFatora;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class FatoraPrintController extends Controller
{
  public function print(Request $request)
  {
    $ids = $request->get('ids');
    if (!$ids)
      return redirect()->back();

    $salesItems = \App\Models\CashierSale::whereIn('cashier_sales_fatora_id', $ids)
      ->with(['variant.product', 'fatora.cashier.user'])
      ->get();

    $totalAmount = $salesItems->sum('full_price');

    if (ob_get_contents())
      ob_end_clean();

    $mpdf = new \Mpdf\Mpdf([
      'fontDir' => array_merge((new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'], [storage_path('fonts')]),
      'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
        'cairo' => [
          'R' => 'Cairo-Bold-2.ttf',
          'useOTL' => 0xFF,
        ]
      ],
      'default_font' => 'cairo',
      'mode' => 'utf-8',
      'format' => 'A4',
      'direction' => 'rtl',
    ]);

    $html = view('cashier.fatora-unified', compact('salesItems', 'totalAmount'))->render();

    $mpdf->WriteHTML($html);
    return $mpdf->Output("unified-sales-report.pdf", 'I');
  }
}
