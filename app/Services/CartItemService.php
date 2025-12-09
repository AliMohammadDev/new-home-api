<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CartItemService
{

  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
    $userId = null
  ): LengthAwarePaginator|Collection {
    $query = CartItem::whereHas('cart', function ($q) use ($userId) {
      $q->where('user_id', $userId);
    });
    if ($paginate) {
      return $query->paginate(
        perPage: $perPage,
        page: $page,
        columns: $columns,
      );
    }
    return $query->get($columns);
  }
  public function addToCart($userId, $productId, $quantity = 1)
  {
    $cart = Cart::firstOrCreate([
      'user_id' => $userId,
      'status' => 'active'
    ]);
    $cartItem = CartItem::where('cart_id', $cart->id)
      ->where('product_id', $productId)
      ->first();
    if ($cartItem) {
      $cartItem->quantity += $quantity;
      $cartItem->save();
      return $cartItem;
    }
    return CartItem::create([
      'cart_id' => $cart->id,
      'product_id' => $productId,
      'quantity' => $quantity
    ]);
  }

  public function updateQuantity(CartItem $cart_item, $quantity)
  {
    if ($cart_item->cart->user_id !== Auth::id()) {
      abort(403, 'Unauthorized');
    }

    $cart_item->update([
      'quantity' => $quantity
    ]);
    return $cart_item;
  }
  public function increaseQuantity(CartItem $cart_item)
  {
    if ($cart_item->cart->user_id !== Auth::id()) {
      abort(403, 'Unauthorized');
    }
    $cart_item->quantity += 1;
    $cart_item->save();
    return $cart_item;
  }


  public function decreaseQuantity(CartItem $cart_item)
  {
    if ($cart_item->cart->user_id !== Auth::id()) {
      abort(403, 'Unauthorized');
    }
    if ($cart_item->quantity <= 1) {
      $cart_item->delete();
      return null;
    }
    $cart_item->quantity -= 1;
    $cart_item->save();
    return $cart_item;
  }


  public function delete(CartItem $cart_item)
  {
    if ($cart_item->cart->user_id !== Auth::id()) {
      abort(403, 'Unauthorized');
    }

    return $cart_item->delete();
  }
}
