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
      'product_id' => 'required|exists:products,id',
    ]);
    $userId = Auth::id();
    $productId = $validatedData['product_id'];
    $newWishList = $this->wishListService->create([
      'user_id' => $userId,
      'product_id' => $productId,
    ]);
    return new WishListResource($newWishList);
  }
  public function destroy(WishList $wishlist)
  {
    $this->wishListService->delete($wishlist);
    return response()->json(['message' => 'Wishlist item removed']);
  }
}
