<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductPackage\CreateProductVariantsPackage;
use App\Http\Requests\ProductPackage\UpdateProductVariantsPackage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductVariantsPackageResource;
use App\Models\ProductVariant;
use App\Models\ProductVariantPackage;
use App\Services\ProductVariantPackageService;

class ProductVariantPackageController extends Controller
{
  public function __construct(
    private ProductVariantPackageService $productVariantPackageService
  ) {
  }

  public function byVariant(ProductVariant $product_variant)
  {
    $packages = $this->productVariantPackageService->getByVariant($product_variant);

    return ProductVariantsPackageResource::collection($packages);
  }

  public function index()
  {
    $categories = $this->productVariantPackageService->findAll();
    return ProductVariantsPackageResource::collection($categories);
  }

  public function store(CreateProductVariantsPackage $request): ProductVariantsPackageResource
  {
    $newPackage = $this->productVariantPackageService->create($request->validated());
    return new ProductVariantsPackageResource($newPackage);
  }

  public function show(ProductVariantPackage $product_variant_package)
  {
    return new ProductVariantsPackageResource($product_variant_package);
  }

  public function update(ProductVariantPackage $product_variant_package, UpdateProductVariantsPackage $request)
  {
    $newColor = $this->productVariantPackageService->update($product_variant_package, $request->validated());
    return new ProductVariantsPackageResource($newColor);
  }
  public function destroy(ProductVariantPackage $product_variant_package)
  {
    $newPackage = $this->productVariantPackageService->delete($product_variant_package);
    return response()->json(['message' => 'Package deleted successfully']);
  }
}