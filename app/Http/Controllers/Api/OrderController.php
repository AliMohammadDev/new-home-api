<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  public function __construct(
    private OrderService $orderService
  ) {
  }
  public function index()
  {
    $orders = $this->orderService->findAll();
    return OrderResource::collection($orders);
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'checkout_id' => ['required', 'exists:checkouts,id'],
      'payment_method' => ['required', 'string'],
    ]);

    $data['user_id'] = auth()->id();

    $order = $this->orderService->placeOrder($data);

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
}
