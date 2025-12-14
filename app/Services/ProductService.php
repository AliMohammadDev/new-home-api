<?php


namespace App\Services;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Cloudinary\Cloudinary;
use App\Models\Product;


class ProductService
{

  public function getSlidersProducts(int $limit = 10)
  {
    // Featured
    $featuredProducts = Product::where('is_featured', true)
      ->take($limit)
      ->get();

    // Slider 2)
    $newProducts = Product::where('created_at', '>=', now()->subDays(30))
      ->take($limit)
      ->get();

    //  Discounted
    $discountedProducts = Product::where('discount', '>', 0)
      ->take($limit)
      ->get();

    return [
      'featured' => $featuredProducts,
      'new' => $newProducts,
      'discounted' => $discountedProducts,
    ];
  }



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


}