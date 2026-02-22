<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImport\CreateProductImportItemRequest;
use App\Http\Requests\ProductImport\UpdateProductImportItemRequest;
use App\Http\Resources\ProductImportItemResource;
use App\Models\ProductImportItem;
use App\Services\Dashboard\ProductImportItemService;
use Illuminate\Http\Request;

class ProductImportItemController extends Controller
{
  public function __construct(
    private ProductImportItemService $productImportItemService
  ) {
  }

  public function index()
  {
    $ProductImportItems = $this->productImportItemService->findAll();
    return ProductImportItemResource::collection($ProductImportItems);
  }

  public function store(CreateProductImportItemRequest $request)
  {
    $ProductImportItem = $this->productImportItemService->create($request->validated());
    return new ProductImportItemResource($ProductImportItem);
  }

  public function show(ProductImportItem $productImportItem)
  {
    return new ProductImportItemResource($productImportItem);
  }

  public function update(ProductImportItem $productImportItem, UpdateProductImportItemRequest $request)
  {
    $newProductImportItem = $this->productImportItemService->update($productImportItem, $request->validated());
    return new ProductImportItemResource($newProductImportItem);
  }
  public function destroy(ProductImportItem $productImportItem)
  {
    $productImportItem = $this->productImportItemService->delete($productImportItem);
    return response()->json(['message' => 'ProductImportItem deleted successfully']);
  }
}