<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Models\Product;

class ProductController extends Controller
{
  public function __construct(
    private ProductService $productService
  ) {
  }

  public function index()
  {
    $products = $this->productService->findAll();
    return ProductResource::collection($products);
  }

  public function store(CreateProductRequest $request)
  {
    $validated = $request->validated();
    $product = $this->productService->create(
      $validated,
      $request->file('image')
    );
    return new ProductResource($product);
  }

  public function show(Product $product)
  {
    return $product;
  }

  public function update(Product $product, UpdateProductRequest $request)
  {
    $validated = $request->validated();

    $newProduct = $this->productService->update(
      $product,
      $validated,
      $request->file('image')
    );

    return new ProductResource($newProduct);
  }
  public function destroy(Product $product)
  {
    $product = $this->productService->delete($product);
    return response()->json(['message' => 'Product deleted successfully']);
  }

}
