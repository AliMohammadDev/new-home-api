<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductVariant\CreateProductVariantRequest;
use App\Http\Requests\ProductVariant\UpdateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Services\ProductVariantService;
use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
  public function __construct(
    private ProductVariantService $productVariantService
  ) {
  }

  // public function byVariantsCategoryName($name)
  // {
  //   $products = $this->productVariantService->findVariantsByCategoryName($name);
  //   return ProductVariantResource::collection($products);
  // }

  public function byVariantsCategoryName(Request $request, $name)
  {
    $filters = [
      'sizes' => $request->query('sizes'),
      'colors' => $request->query('colors'),
      'materials' => $request->query('materials'),
      'min_price' => $request->query('min_price'),
      'max_price' => $request->query('max_price'),
    ];

    $products = $this->productVariantService->findVariantsByCategoryName($name, $filters);

    return ProductVariantResource::collection($products);
  }

  public function slidersVariants()
  {
    $sliders = $this->productVariantService->getSlidersProductsVariants();
    return response()->json([
      'featured' => ProductVariantResource::collection($sliders['featured']),
      'new' => ProductVariantResource::collection($sliders['new']),
      'discounted' => ProductVariantResource::collection($sliders['discounted']),
      'top_rated' => ProductVariantResource::collection($sliders['top_rated']),
    ]);
  }



    public function index()
    {
      $variants = $this->productVariantService->findAll();
      return ProductVariantResource::collection($variants);
    }

  public function store(CreateProductVariantRequest $request)
  {
    $variant = $this->productVariantService->create($request->validated());
    return new ProductVariantResource($variant);
  }

  public function show(ProductVariant $product_variant)
  {
    $variant = $this->productVariantService->show($product_variant);
    return new ProductVariantResource($variant);
  }

  public function update(UpdateProductVariantRequest $request, ProductVariant $product_variant)
  {
    $variant = $this->productVariantService->update($request->validated(), $product_variant);
    return new ProductVariantResource($variant);
  }

  public function destroy(ProductVariant $product_variant)
  {
    $this->productVariantService->delete($product_variant);
    return response()->noContent();
  }

}