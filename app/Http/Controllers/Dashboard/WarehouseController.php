<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\CreateWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use App\Services\Dashboard\WarehouseService;

class WarehouseController extends Controller
{
  public function __construct(
    private WarehouseService $warehouseServiceService
  ) {
  }

  public function index()
  {
    $shipping = $this->warehouseServiceService->findAll();
    return WarehouseResource::collection($shipping);
  }

  public function store(CreateWarehouseRequest $request)
  {
    $validated = $request->validated();
    $warehouse = $this->warehouseServiceService->create(
      $validated,
    );
    return new WarehouseResource($warehouse);
  }

  public function show(Warehouse $warehouse)
  {
    $warehouse->load('productVariants');
    return new WarehouseResource($warehouse);
  }

  public function update(Warehouse $shippingCity, UpdateWarehouseRequest $request)
  {
    $validated = $request->validated();
    $newWarehouse = $this->warehouseServiceService->update(
      $shippingCity,
      $validated,
    );
    return new WarehouseResource($newWarehouse);
  }
  public function destroy(Warehouse $warehouse)
  {
    $warehouse = $this->warehouseServiceService->delete($warehouse);
    return response()->json(['message' => 'Shipping deleted successfully']);
  }
}