<?php


namespace App\Services;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Cloudinary\Cloudinary;
use App\Models\Product;


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

  public function create(array $data, $imageFile = null)
  {
    if ($imageFile) {
      $cloudinary = new Cloudinary(config('cloudinary.url'));
      $uploaded = $cloudinary->uploadApi()->upload(
        $imageFile->getRealPath(),
        ['folder' => 'products']
      );
      $data['image'] = $uploaded['secure_url'];
      $data['image_public_id'] = $uploaded['public_id'];
    }
    return Product::create($data);
  }

  public function show(Product $product)
  {
    return $product;
  }

  public function update(Product $product, array $data, $imageFile = null)
  {
    if ($imageFile) {
      $cloudinary = new Cloudinary(config('cloudinary.url'));
      if ($product->image_public_id) {
        $cloudinary->uploadApi()->destroy($product->image_public_id);
      }
      $uploaded = $cloudinary->uploadApi()->upload(
        $imageFile->getRealPath(),
        ['folder' => 'products']
      );
      $data['image'] = $uploaded['secure_url'];
      $data['image_public_id'] = $uploaded['public_id'];
    }
    $product->update($data);
    return $product;
  }

  public function delete(Product $product)
  {
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
