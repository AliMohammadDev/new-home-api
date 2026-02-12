<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderProcessed;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;




class OrderController extends Controller
{
  public function __construct(
    private OrderService $orderService
  ) {
  }
  public function index(Request $request)
  {
    $orders = $this->orderService->findAll(
      paginate: true,
      perPage: $request->get('per_page', 5),
      page: $request->get('page', 1),
    );

    return OrderResource::collection($orders);
  }


  public function store(Request $request)
  {
    $data = $request->validate([
      'checkout_id' => ['required', 'exists:checkouts,id'],
      'payment_method' => ['required', 'string'],
    ]);

    $data['user_id'] = Auth::id();

    $order = $this->orderService->placeOrder($data);

    // fire event OrderProcessed
    broadcast(new OrderProcessed($order));

    $admins = User::where('role', 'admin')->get();

    foreach ($admins as $admin) {
      $admin->notify(new NewOrderNotification($order));
    }
    return new OrderResource($order);
  }

  public function show($id)
  {
    $order = $this->orderService->showOrder($id);
    return new OrderResource($order);
  }
  public function update(Request $request, Order $order)
  {
    $data = $request->validate([
      'status' => ['required', 'string']
    ]);

    $order = $this->orderService->updateOrderStatus($order, $data);

    return new OrderResource($order);
  }
  public function destroy(Order $order)
  {
    $order = $this->orderService->cancelOrder($order);
    return new OrderResource($order);
  }

  public function print(Order $order)
  {
    if (ob_get_contents())
      ob_end_clean();

    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    try {
      $fontFolders = array_merge($fontDirs, [
        storage_path('fonts'),
      ]);

      $mpdf = new \Mpdf\Mpdf([
        'fontDir' => $fontFolders,
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
        'img_dpi' => 96,
        'showImageErrors' => true
      ]);

      $mpdf->SetDirectionality('rtl');
      $html = view('orders.print-invoice', compact('order'))->render();

      $mpdf->WriteHTML($html);
      return $mpdf->Output("invoice-{$order->id}.pdf", 'I');

    } catch (\Mpdf\MpdfException $e) {
      return $e->getMessage();
    }
  }
}