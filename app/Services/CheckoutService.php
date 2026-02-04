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

  public function createNewCheckout(array $data, $userId)
  {
    $cart = Cart::where('id', $data['cart_id'])
      ->where('user_id', $userId)
      ->firstOrFail();

    return Checkout::create([
      'user_id' => $userId,
      'cart_id' => $cart->id,
      'first_name' => $data['first_name'],
      'last_name' => $data['last_name'],
      'email' => $data['email'],
      'phone' => $data['phone'],
      'country' => $data['country'],
      'city' => $data['city'],
      'shipping_city_id' => $data['shipping_city_id'] ?? null,
      'street' => $data['street'] ?? '',
      'floor' => $data['floor'] ?? null,
      'postal_code' => $data['postal_code'] ?? null,
      'additional_information' => $data['additional_information'] ?? null,
      'status' => 'pending',
    ]);
  }

  public function createCheckout(array $data, $userId)
  {
    $cart = Cart::where('id', $data['cart_id'])
      ->where('user_id', $userId)
      ->firstOrFail();
    return Checkout::updateOrCreate(
      [
        'cart_id' => $cart->id,
        'user_id' => $userId,
      ],
      [
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'country' => $data['country'],
        'city' => $data['city'],
        'shipping_city_id' => $data['shipping_city_id'] ?? null,
        'street' => $data['street'] ?? '',
        'floor' => $data['floor'] ?? null,
        'postal_code' => $data['postal_code'] ?? null,
        'additional_information' => $data['additional_information'] ?? null,
        'status' => 'pending',
      ]
    );
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