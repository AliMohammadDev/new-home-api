<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShippingWarehouse;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class ShippingWarehouseController extends Controller
{
  public function print(Request $request)
  {
    $ids = $request->get('ids');
    if (!$ids)
      return redirect()->back();

    $records = ShippingWarehouse::whereIn('id', $ids)
      ->with(['warehouse', 'productVariant.product'])
      ->get();

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
        'autoArabic' => true,
        'autoScriptToLang' => true,
        'autoLangToFont' => true,
      ]);

      $mpdf->SetDirectionality('rtl');

      $html = view('shipping.print-shipping', compact('records'))
        ->render();

      $mpdf->WriteHTML($html);
      return $mpdf->Output("shipping-manifest.pdf", 'I');

    } catch (\Mpdf\MpdfException $e) {
      return $e->getMessage();
    }
  }
}