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
    $query = $query = CartItem::with([
      'productVariant.product.variants.color',
      'productVariant.product.variants.size',
      'productVariant.product.variants.material',
      'productVariant.images',
      'productVariantPackage',
      'cart'
    ])->whereHas('cart', function ($q) use ($userId) {
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
  public function addToCart($userId, $product_variant_id, $quantity = 1, $package_id = null)
  {
    $cart = Cart::firstOrCreate([
      'user_id' => $userId,
      'status' => 'active'
    ]);
    $cartItem = CartItem::where('cart_id', $cart->id)
      ->where('product_variant_id', $product_variant_id)
      ->where('product_variant_package_id', $package_id)
      ->first();
    if ($cartItem) {
      $cartItem->quantity += $quantity;
      $cartItem->save();
      return $cartItem;
    }
    return CartItem::create([
      'cart_id' => $cart->id,
      'product_variant_id' => $product_variant_id,
      'product_variant_package_id' => $package_id,
      'quantity' => $quantity
    ]);
  }

  public function update(CartItem $cart_item, array $data)
  {
    if ($cart_item->cart->user_id !== Auth::id()) {
      abort(403, 'Unauthorized');
    }
    $cart_item->fill($data);

    if (isset($data['type']) && $data['type'] === 'Individual') {
      $cart_item->product_variant_package_id = null;
    }
    $cart_item->save();

    return $cart_item;
  }
  public function increaseQuantity(CartItem $cart_item)
  {
    if ($cart_item->cart->user_id !== Auth::id()) {
      abort(403, 'Unauthorized');
    }

    $availableStock = $cart_item->productVariant->stock_quantity;

    if ($cart_item->quantity + 1 > $availableStock) {
      abort(422, "Sorry, only {$availableStock} items are available in stock.");
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
