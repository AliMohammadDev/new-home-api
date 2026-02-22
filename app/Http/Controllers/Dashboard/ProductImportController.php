<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImport\CreateProductImportRequest;
use App\Http\Requests\ProductImport\UpdateProductImportRequest;
use App\Http\Resources\ProductImportResource;
use App\Models\ProductImport;
use App\Services\Dashboard\ProductImportService;
use Illuminate\Http\Request;

class ProductImportController extends Controller
{
  public function __construct(
    private ProductImportService $productImportService
  ) {
  }

  public function index()
  {
    $ProductImports = $this->productImportService->findAll();
    return ProductImportResource::collection($ProductImports);
  }

  public function store(CreateProductImportRequest $request)
  {
    $ProductImport = $this->productImportService->create($request->validated());
    return new ProductImportResource($ProductImport);
  }

  public function show(ProductImport $productImport)
  {
    return new ProductImportResource($productImport);
  }

  public function update(ProductImport $productImport, UpdateProductImportRequest $request)
  {
    $newProductImport = $this->productImportService->update($productImport, $request->validated());
    return new ProductImportResource($newProductImport);
  }
  public function destroy(ProductImport $productImport)
  {
    $productImport = $this->productImportService->delete($productImport);
    return response()->json(['message' => 'ProductImport deleted successfully']);
  }
}