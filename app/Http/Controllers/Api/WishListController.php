<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\WishListResource;
use App\Http\Controllers\Controller;
use App\Services\WishListService;
use Illuminate\Http\Request;
use App\Models\WishList;
use Illuminate\Support\Facades\Auth;

class WishListController extends Controller
{
  public function __construct(
    private WishListService $wishListService
  ) {
  }
  public function index()
  {
    $wishlist = $this->wishListService->findAll(userId: Auth::id());
    return WishListResource::collection($wishlist);
  }

  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'product_variant_id' => 'required|exists:product_variants,id',
    ]);
    $userId = Auth::id();
    $productId = $validatedData['product_variant_id'];
    $newWishList = $this->wishListService->create(data: [
      'user_id' => $userId,
      'product_variant_id' => $productId,
    ]);
    return new WishListResource($newWishList);
  }
  public function destroy(WishList $wishlist)
  {
    $this->wishListService->delete($wishlist);
    return response()->json(['message' => 'Wishlist item removed']);
  }
}
