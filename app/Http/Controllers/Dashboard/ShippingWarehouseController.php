<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingWarehouse\CreateShippingWarehouseRequest;
use App\Http\Requests\ShippingWarehouse\UpdateShippingWarehouseRequest;
use App\Http\Resources\ShippingWarehouseResource;
use App\Models\ShippingWarehouse;
use App\Services\Dashboard\ShippingWarehouseService;

class ShippingWarehouseController extends Controller
{
  public function __construct(
    private ShippingWarehouseService $shippingWarehouseService
  ) {
  }

  public function index()
  {
    $shippingWarehouses = $this->shippingWarehouseService->findAll();
    return ShippingWarehouseResource::collection($shippingWarehouses);
  }

  public function store(CreateShippingWarehouseRequest $request)
  {
    $validated = $request->validated();
    $shippingWarehouses = $this->shippingWarehouseService->create(
      $validated,
    );
    return new ShippingWarehouseResource($shippingWarehouses);
  }

  public function show(ShippingWarehouse $shippingWarehouse)
  {
    $shippingWarehouse->load(['warehouse', 'productVariant']);
    return new ShippingWarehouseResource($shippingWarehouse);
  }

  public function update(ShippingWarehouse $shippingWarehouse, UpdateShippingWarehouseRequest $request)
  {
    $validated = $request->validated();
    $updatedEntry = $this->shippingWarehouseService->update($shippingWarehouse, $validated);
    return new ShippingWarehouseResource($updatedEntry);
  }

  public function destroy(ShippingWarehouse $shippingWarehouse)
  {
    $this->shippingWarehouseService->delete($shippingWarehouse);
    return response()->json(['message' => 'Entry deleted successfully']);
  }


}
