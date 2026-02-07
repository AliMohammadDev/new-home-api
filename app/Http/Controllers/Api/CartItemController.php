<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CartItem\CreateCartItemRequest;
use App\Http\Requests\CartItem\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\CartItemService;
use App\Models\CartItem;
use App\Models\Checkout;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
  public function __construct(
    private CartItemService $cartItemService
  ) {
  }

  public function index(Request $request)
  {
    $userId = Auth::id();
    $checkoutId = $request->query('checkout_id');

    $items = $this->cartItemService->findAll(userId: $userId);
    $cartItems = CartItemResource::collection($items);

    $subtotal = round(
      $items->sum(function ($item) {
        $price = ($item->product_variant_package_id && $item->productVariantPackage)
          ? $item->productVariantPackage->price
          : $item->productVariant->final_price;
        return $item->quantity * $price;
      }),
      2
    );

    $shippingFee = 0;
    if ($checkoutId) {
      $checkout = Checkout::with('shippingCity')
        ->where('id', $checkoutId)
        ->where('user_id', $userId)
        ->first();

      if ($checkout && $checkout->shippingCity) {
        if (!$checkout->shippingCity->is_free_shipping) {
          $shippingFee = $checkout->shippingCity->shipping_fee;
        }
      }
    }

    $grandTotal = round($subtotal + $shippingFee, 2);

    return response()->json([
      'data' => $cartItems,
      'subtotal' => $subtotal,
      'shipping_fee' => $shippingFee,
      'cart_total' => $grandTotal,
    ]);
  }

  public function store(CreateCartItemRequest $request)
  {
    $validated = $request->validated();
    $cartItem = $this->cartItemService->addToCart(
      Auth::id(),
      $validated['product_variant_id'],
      $validated['quantity'] ?? 1,
      $validated['product_variant_package_id'] ?? null
    );
    return new CartItemResource($cartItem);
  }

  public function update(CartItem $cart_item, UpdateCartItemRequest $request)
  {
    $validated = $request->validated();
    $updatedItem = $this->cartItemService->update($cart_item, $validated);
    return new CartItemResource($updatedItem);
  }
  public function increase(CartItem $cart_item)
  {
    $item = $this->cartItemService->increaseQuantity($cart_item);
    return new CartItemResource($item);
  }
  public function decrease(CartItem $cart_item)
  {
    $item = $this->cartItemService->decreaseQuantity($cart_item);
    if (!$item) {
      return response()->json([
        'message' => 'Item removed from cart',
      ], 200);
    }

    return new CartItemResource($item);
  }
  public function destroy(CartItem $cart_item)
  {
    $this->cartItemService->delete($cart_item);
    return response()->json(['message' => 'Cart item removed']);
  }
}