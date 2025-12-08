<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\CreateCheckoutRequest;
use App\Http\Requests\Checkout\UpdateCheckoutRequest;
use App\Http\Resources\CheckoutResource;
use App\Models\Checkout;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
  public function __construct(
    private CheckoutService $checkoutService
  ) {
  }
  public function index()
  {
    $checkouts = $this->checkoutService->findAll(userId: auth()->id());
    return CheckoutResource::collection($checkouts);
  }

  public function store(CreateCheckoutRequest $request)
  {
    $checkout = $this->checkoutService->createCheckout(
      $request->validated(),
      auth()->id()
    );
    return new CheckoutResource($checkout);
  }


  public function update(UpdateCheckoutRequest $request, Checkout $checkout)
  {
    if ($checkout->user_id !== auth()->id()) {
      abort(403, 'Unauthorized');
    }
    $updated = $this->checkoutService->updateCheckout(
      $checkout,
      $request->validated()
    );

    return new CheckoutResource($updated);
  }
  public function show(Checkout $checkout)
  {
    if ($checkout->user_id !== auth()->id()) {
      abort(403, 'Unauthorized');
    }
    return new CheckoutResource($checkout);
  }

  public function destroy(Checkout $checkout)
  {
    if ($checkout->user_id !== auth()->id()) {
      abort(403, 'Unauthorized');
    }
    $this->checkoutService->deleteCheckout($checkout);
    return response()->json(['message' => 'Checkout deleted']);
  }
}
