<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Checkout;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CheckoutService
{
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
    $userId = null
  ): LengthAwarePaginator|Collection {
    $query = Checkout::query();
    if ($userId) {
      $query->where('user_id', $userId);
    }
    if ($paginate) {
      return $query->paginate(
        perPage: $perPage,
        page: $page,
        columns: $columns,
      );
    }
    return $query->get($columns);
  }

  public function createCheckout(array $data, $userId)
  {
    $cart = Cart::where('id', $data['cart_id'])
      ->where('user_id', $userId)
      ->firstOrFail();
    return Checkout::create([
      'first_name' => $data['first_name'],
      'last_name' => $data['last_name'],
      'city' => $data['city'],
      'address' => $data['address'],
      'cart_id' => $cart->id,
      'user_id' => $userId,
      'status' => 'pending',
    ]);
  }

  public function updateCheckout(Checkout $checkout, array $data)
  {
    $checkout->update($data);
    return $checkout;
  }

  public function deleteCheckout(Checkout $checkout)
  {
    return $checkout->delete();
  }
}
