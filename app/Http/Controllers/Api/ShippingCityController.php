<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingCity\CreateShippingCityRequest;
use App\Http\Requests\ShippingCity\UpdateShippingCityRequest;
use App\Http\Resources\ShippingCityResource;
use App\Models\ShippingCity;
use App\Services\ShippingCityService;

class ShippingCityController extends Controller
{
  public function __construct(
    private ShippingCityService $ShippingCityService
  ) {
  }

  public function index()
  {
    $shipping = $this->ShippingCityService->findAll();
    return ShippingCityResource::collection($shipping);
  }

  public function store(CreateShippingCityRequest $request)
  {
    $validated = $request->validated();
    $shipping = $this->ShippingCityService->create(
      $validated,
    );
    return new ShippingCityResource($shipping);
  }

  public function show(ShippingCity $shippingCity)
  {
    $shippingCity->load(['checkouts']);
    return new ShippingCityResource($shippingCity);
  }

  public function update(ShippingCity $shippingCity, UpdateShippingCityRequest $request)
  {
    $validated = $request->validated();
    $newShipping = $this->ShippingCityService->update(
      $shippingCity,
      $validated,
    );
    return new ShippingCityResource($newShipping);
  }
  public function destroy(ShippingCity $shippingCity)
  {
    $shipping = $this->ShippingCityService->delete($shippingCity);
    return response()->json(['message' => 'Shipping deleted successfully']);
  }
}