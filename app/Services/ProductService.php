<?php


namespace App\Services;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Cloudinary\Cloudinary;
use App\Models\Product;


class ProductService
{

  public function findProductsByCategoryName(string $categoryName)
  {
    return Product::with(['category'])
      ->whereHas('category', function ($query) use ($categoryName) {
        $query->where('name', $categoryName);
      })
      ->get();
  }


  public function getAllProductsByLimit(int $limit = 10)
  {
    return Product::with(['category'])
      ->take($limit)
      ->get();
  }

  public function getSlidersProducts(int $limit = 10)
  {
    $baseQuery = Product::with(['category']);
    return [
      'featured' => (clone $baseQuery)
        ->where('is_featured', true)
        ->take($limit)
        ->get(),

      'new' => (clone $baseQuery)
        ->where('created_at', '>=', now()->subDays(30))
        ->take($limit)
        ->get(),

      'discounted' => (clone $baseQuery)
        ->where('discount', '>', 0)
        ->take($limit)
        ->get(),
    ];
  }



  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    // $query = Product::with(['category']);
    $query = Product::with([
      'category.media',
      'media',  
    ]);


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
    // if ($imageFile) {
    //   $cloudinary = new Cloudinary(config('cloudinary.url'));
    //   $uploaded = $cloudinary->uploadApi()->upload(
    //     $imageFile->getRealPath(),
    //     ['folder' => 'products']
    //   );
    //   $data['image'] = $uploaded['secure_url'];
    //   $data['image_public_id'] = $uploaded['public_id'];
    // }
    return Product::create($data);
  }

  public function show(Product $product)
  {
    return $product;
  }

  public function update(Product $product, array $data, $imageFile = null)
  {
    // if ($imageFile) {
    //   $cloudinary = new Cloudinary(config('cloudinary.url'));
    //   if ($product->image_public_id) {
    //     $cloudinary->uploadApi()->destroy($product->image_public_id);
    //   }
    //   $uploaded = $cloudinary->uploadApi()->upload(
    //     $imageFile->getRealPath(),
    //     ['folder' => 'products']
    //   );
    //   $data['image'] = $uploaded['secure_url'];
    //   $data['image_public_id'] = $uploaded['public_id'];
    // }
    $product->update($data);
    return $product;
  }

  public function delete(Product $product)
  {
    // if ($product->image_public_id) {
    //   $cloudinary = new Cloudinary(config('cloudinary.url'));
    //   $cloudinary->uploadApi()->destroy($product->image_public_id);
    // }

    return $product->delete();
  }


}