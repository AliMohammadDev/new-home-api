<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\ProductImportItem; // أضف هذا
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class SupplierImportController extends Controller
{
  public function print(Request $request)
  {
    $ids = $request->get('ids');
    if (!$ids)
      return redirect()->back();

    $records = ProductImportItem::whereIn('id', $ids)
      ->with([
        'productVariant.product',
        'productVariant.color',
        'productVariant.size',
        'productVariant.material',
        'productImport'
      ])
      ->get();

    if (ob_get_contents())
      ob_end_clean();

    $mpdf = new Mpdf([
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

    $html = view('shipping.supplier-imports', compact('records'))->render();

    $mpdf->WriteHTML($html);
    return $mpdf->Output("supplier-items.pdf", 'I');
  }
}
