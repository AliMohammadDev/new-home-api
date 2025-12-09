<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Checkout;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = Order::where('user_id', Auth::id())
      ->with(['user', 'checkout', 'orderItems.product']);

    if ($paginate) {
      return $query->paginate(
        perPage: $perPage,
        page: $page,
        columns: $columns,
      );
    }
    return $query->get($columns);
  }
  public function placeOrder(array $data)
  {
    return DB::transaction(function () use ($data) {

      $checkout = Checkout::with('cart.cartItems.product')
        ->where('id', $data['checkout_id'])
        ->where('user_id', $data['user_id'])
        ->firstOrFail();

      $cart = $checkout->cart;

      $total = 0;
      foreach ($cart->cartItems as $item) {
        $total += $item->product->final_price * $item->quantity;
      }

      $order = Order::create([
        'user_id' => $checkout->user_id,
        'cart_id' => $checkout->cart_id,
        'checkout_id' => $checkout->id,
        'total_amount' => $total,
        'payment_method' => $data['payment_method'],
        'status' => 'pending',
      ]);

      foreach ($cart->cartItems as $item) {
        OrderItem::create([
          'order_id' => $order->id,
          'product_id' => $item->product_id,
          'quantity' => $item->quantity,
          'price' => $item->product->price,
          'total' => $item->product->final_price * $item->quantity,
        ]);
      }

      $cart->cartItems()->delete();

      $cart->update(['status' => 'disabled']);


      Cart::create([
        'user_id' => $checkout->user_id,
        'status' => 'active',
      ]);

      return $order->load(['orderItems.product', 'checkout']);
    });
  }

  public function showOrder($orderId)
  {
    return Order::with(['orderItems.product', 'checkout', 'user'])
      ->findOrFail($orderId);
  }

  public function updateOrderStatus(Order $order, array $data)
  {
    $order->update([
      'status' => $data['status']
    ]);
    return $order;
  }

  public function cancelOrder(Order $order)
  {
    $order->update(['status' => 'cancelled']);
    return $order;
  }
}
