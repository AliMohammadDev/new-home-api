<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItem\CreateCartItemRequest;
use App\Http\Requests\CartItem\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
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
    $items = $this->cartItemService->findAll(userId: auth()->id());
    return CartItemResource::collection($items);
  }

  public function store(CreateCartItemRequest $request)
  {
    $validated = $request->validated();
    $cartItem = $this->cartItemService->addToCart(
      auth()->id(),
      $validated['product_id'],
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
    return new CartItemResource($item);
  }


  public function destroy(CartItem $cart_item)
  {
    $this->cartItemService->delete($cart_item);

    return response()->json(['message' => 'Cart item removed']);
  }
}
