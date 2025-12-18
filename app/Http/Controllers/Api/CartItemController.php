<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItem\CreateCartItemRequest;
use App\Http\Requests\CartItem\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use Illuminate\Support\Facades\Auth;
use App\Services\CartItemService;
use App\Models\CartItem;

class CartItemController extends Controller
{
  public function __construct(
    private CartItemService $cartItemService
  ) {
  }

  public function index()
  {
    $items = $this->cartItemService->findAll(userId: Auth::id());

    $cartItems = CartItemResource::collection($items);

    $cartTotal = round(
      $items->sum(function ($item) {
        return $item->quantity * $item->productVariant->product->final_price;
      }),
      2
    );

    return response()->json([
      'data' => $cartItems,
      'cart_total' => $cartTotal,
    ]);
  }

  public function store(CreateCartItemRequest $request)
  {
    $validated = $request->validated();
    $cartItem = $this->cartItemService->addToCart(
      Auth::id(),
      $validated['product_variant_id'],
      $validated['quantity'] ?? 1
    );
    return new CartItemResource($cartItem);
  }

  public function update(CartItem $cart_item, UpdateCartItemRequest $request)
  {
    $validated = $request->validated();
    $updatedItem = $this->cartItemService->updateQuantity($cart_item, $validated['quantity']);
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