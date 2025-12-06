<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\ColorResource;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\SizeResource;
use App\Services\ProductService;
use App\Models\Product;
use Illuminate\Http\Request;

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
  public function attachColor(Request $request, Product $product)
  {
    $request->validate([
      'color_id' => 'required|exists:colors,id',
    ]);
    $product = $this->productService->attachColor($product, $request->color_id);
    return ColorResource::collection($product->colors);
  }

  public function attachSize(Request $request, Product $product)
  {
    $request->validate([
      'size_id' => 'required|exists:sizes,id',
    ]);
    $product = $this->productService->attachSize($product, $request->size_id);
    return SizeResource::collection($product->sizes);
  }

  public function attachMaterial(Request $request, Product $product)
  {
    $request->validate([
      'material_id' => 'required|exists:materials,id',
    ]);
    $product = $this->productService->attachMaterial($product, $request->material_id);
    return MaterialResource::collection($product->materials);
  }
  public function detachColor(Request $request, Product $product)
  {
    $request->validate([
      'color_id' => 'required|exists:colors,id',
    ]);

    $product = $this->productService->detachColor($product, $request->color_id);

    return ColorResource::collection($product->colors);
  }

  public function detachSize(Request $request, Product $product)
  {
    $request->validate([
      'size_id' => 'required|exists:sizes,id',
    ]);

    $product = $this->productService->detachSize($product, $request->size_id);

    return SizeResource::collection($product->sizes);
  }

  public function detachMaterial(Request $request, Product $product)
  {
    $request->validate([
      'material_id' => 'required|exists:materials,id',
    ]);

    $product = $this->productService->detachMaterial($product, $request->material_id);

    return MaterialResource::collection($product->materials);
  }

}
