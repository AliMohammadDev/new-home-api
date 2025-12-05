<?php


namespace App\Services;

use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use Cloudinary\Cloudinary;


class ProductService
{
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = Product::query();

    if ($paginate) {
      return $query->paginate(
        perPage: $perPage,
        page: $page,
        columns: $columns,
      );
    }
    return $query->get($columns);
  }

  public function create(CreateProductRequest $data)
  {

    $validated = $data->validated();
    // if ($data->hasFile('image')) {
    //   $validated['image'] = $data->file('image')->store('products', 'public');
    // }
    if ($data->hasFile('image')) {
      $cloudinary = new Cloudinary(config('cloudinary.url'));

      $uploaded = $cloudinary->uploadApi()->upload(
        $data->file('image')->getRealPath(),
        ['folder' => 'products']
      );
      $validated['image'] = $uploaded['secure_url'];
      $validated['image_public_id'] = $uploaded['public_id'];
    }
    return Product::create($validated);
  }

  public function show(Product $product)
  {
    return $product;
  }

  public function update(Product $product, UpdateProductRequest $data)
  {
    $validated = $data->validated();

    // if ($data->hasFile('image')) {
    //   if ($product->image) {
    //     Storage::disk('public')->delete($product->image);
    //   }
    //   $validated['image'] = $data->file('image')->store('products', 'public');
    // }

    $cloudinary = new Cloudinary(config('cloudinary.url'));
    if ($data->hasFile('image')) {
      if ($product->image_public_id) {
        $cloudinary->uploadApi()->destroy($product->image_public_id);
      }
      $uploaded = $cloudinary->uploadApi()->upload(
        $data->file('image')->getRealPath(),
        ['folder' => 'products']
      );

      $validated['image'] = $uploaded['secure_url'];
      $validated['image_public_id'] = $uploaded['public_id'];
    }


    $product->update($validated);
    return $product;
  }

  public function delete(Product $product)
  {
    // if ($product->image) {
    //   Storage::disk('public')->delete($product->image);
    // }
    if ($product->image_public_id) {
      $cloudinary = new Cloudinary(config('cloudinary.url'));
      $cloudinary->uploadApi()->destroy($product->image_public_id);
    }

    return $product->delete();
  }

  public function attachColor(Product $product, string $color_id)
  {
    $product->colors()->syncWithoutDetaching([$color_id]);
    return $product->load('colors');
  }

  public function attachSize(Product $product, string $size_id)
  {
    $product->sizes()->syncWithoutDetaching([$size_id]);
    return $product->load('sizes');
  }
  public function attachMaterial(Product $product, string $material_id)
  {
    $product->materials()->syncWithoutDetaching([$material_id]);
    return $product->load('materials');
  }

  public function detachColor(Product $product, string $color_id)
  {
    $product->colors()->detach($color_id);
    return $product->load('colors');
  }

  public function detachSize(Product $product, string $size_id)
  {
    $product->sizes()->detach($size_id);
    return $product->load('sizes');
  }

  public function detachMaterial(Product $product, string $material_id)
  {
    $product->materials()->detach($material_id);
    return $product->load('materials');
  }


}
