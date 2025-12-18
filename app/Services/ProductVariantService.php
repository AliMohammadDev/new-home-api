<?php
namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductVariantService
{


  public function findVariantsByCategoryName(string $categoryName)
  {
    return ProductVariant::with([
      'product' => function ($query) use ($categoryName) {
        $query->select('id', 'name', 'image', 'price', 'discount', 'category_id')
          ->with(['category:id,name,image'])
          ->whereHas('category', fn($q) => $q->where('name', $categoryName));
      },
      'color:id,color',
      'size:id,size',
      'material:id,material'
    ])
      ->withAvg('reviews', 'rating')
      ->withCount('reviews')
      ->whereIn('id', function ($q) {
        $q->select(DB::raw('MIN(id)'))
          ->from('product_variants')
          ->groupBy('product_id');
      })
      ->get()
      ->filter(fn($variant) => $variant->product !== null);

  }



  public function getAllProductVariantsByLimit(int $limit = 10)
  {
    return ProductVariant::with([
      'product' => fn($q) => $q->select('id', 'name', 'image', 'price', 'discount', 'category_id')
        ->with(['category:id,name,image']),
      'color:id,color',
      'size:id,size',
      'material:id,material',
    ])->withAvg('reviews', 'rating')
      ->withCount('reviews')
      ->whereIn('id', function ($q) {
        $q->select(DB::raw('MIN(id)'))
          ->from('product_variants')
          ->groupBy('product_id');
      })
      ->take($limit)
      ->get();
  }


  public function getSlidersProductsVariants(int $limit = 10)
  {
    $baseQuery = ProductVariant::with([
      'product' => fn($q) => $q->select('id', 'name', 'image', 'price', 'discount', 'category_id')
        ->with(['category:id,name,image']),
      'color:id,color',
      'size:id,size',
      'material:id,material',
    ])->withAvg('reviews', 'rating')
      ->withCount('reviews')
      ->whereIn('id', function ($q) {
        $q->select(DB::raw('MIN(id)'))
          ->from('product_variants')
          ->groupBy('product_id');
      });

    return [
      'featured' => (clone $baseQuery)
        ->whereHas('product', fn($q) => $q->where('is_featured', true))
        ->take($limit)
        ->get(),

      'new' => (clone $baseQuery)
        ->whereHas('product', fn($q) => $q->where('created_at', '>=', now()->subDays(30)))
        ->take($limit)
        ->get(),

      'discounted' => (clone $baseQuery)
        ->whereHas('product', fn($q) => $q->where('discount', '>', 0))
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
    $query = ProductVariant::with([
      'product' => fn($q) => $q
        ->select('id', 'category_id', 'name', 'image', 'price', 'discount', 'created_at', 'is_featured')
        ->with('category:id,name,image'),
      'color:id,color',
      'size:id,size',
      'material:id,material',
    ])->withAvg('reviews', 'rating')
      ->withCount('reviews')
      ->whereIn('id', function ($q) {
        $q->select(DB::raw('MIN(id)'))
          ->from('product_variants')
          ->groupBy('product_id');
      });



    if ($paginate) {
      return $query->paginate(
        perPage: $perPage,
        page: $page,
        columns: $columns,
      );
    }
    return $query->get($columns);
  }


  public function create(array $data)
  {
    return ProductVariant::create($data);
  }

  public function update(array $data, ProductVariant $product_variant)
  {
    $product_variant->update($data);
    return $product_variant->fresh();
  }

  public function show(ProductVariant $product_variant)
  {
    return $product_variant
      ->load(['product', 'color', 'size', 'material'])
      ->loadAvg('reviews', 'rating')
      ->loadCount('reviews');
  }

  public function delete(ProductVariant $product_variant)
  {
    return $product_variant->delete();
  }

}
