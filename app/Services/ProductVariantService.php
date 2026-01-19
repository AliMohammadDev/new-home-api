<?php
namespace App\Services;

use App\Models\ProductVariant;
use App\Models\ProductVariantImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ProductVariantService
{

  public function findVariantsByCategoryName(int $categoryName)
  {
    return ProductVariant::with([
      'product' => function ($query) use ($categoryName) {
        $query->select('id', 'name', 'category_id')
          ->with(['category:id,name'])
          ->whereHas('category', function ($q) use ($categoryName) {
            $q->where('id', $categoryName);
          });
      },
      'color:id,color',
      'size:id,size',
      'material:id,material',
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
      'product' => fn($q) => $q->select('id', 'name', 'category_id')
        ->with(['category:id,name']),
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
      'product' => fn($q) => $q->select('id', 'name', 'category_id')
        ->with(['category:id,name']),
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
        ->select('id', 'category_id', 'name', 'created_at', 'is_featured')
        ->with('category:id,name'),
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
    return DB::transaction(function () use ($data) {
      $variant = ProductVariant::create([
        'product_id' => $data['product_id'],
        'color_id' => $data['color_id'],
        'size_id' => $data['size_id'],
        'material_id' => $data['material_id'],
        'price' => $data['price'],
        'discount' => $data['discount'] ?? 0,
        'stock_quantity' => $data['stock_quantity'],
        'image' => '',
      ]);

      if (isset($data['images']) && is_array($data['images'])) {
        foreach ($data['images'] as $index => $imageFile) {
          $filename = Str::uuid() . '.webp';

          $img = Image::make($imageFile)
            ->resize(1000, 1000, function ($constraint) {
              $constraint->aspectRatio();
              $constraint->upsize();
            })
            ->encode('webp', 70);

          Storage::disk('public')->put('product_variants/' . $filename, $img);

          ProductVariantImage::create([
            'product_variant_id' => $variant->id,
            'image' => $filename,
          ]);

          if ($index === 0) {
            $variant->update(['image' => $filename]);
          }
        }
      }

      return $variant->load('images');
    });
  }

  public function update(array $data, ProductVariant $product_variant)
  {
    return DB::transaction(function () use ($data, $product_variant) {
      $product_variant->update($data);

      if (isset($data['images']) && is_array($data['images'])) {
        foreach ($data['images'] as $index => $imageFile) {
          $filename = Str::uuid() . '.webp';

          $img = Image::make($imageFile)
            ->resize(1000, 1000, function ($constraint) {
              $constraint->aspectRatio();
              $constraint->upsize();
            })
            ->encode('webp', 70);

          Storage::disk('public')->put('product_variants/' . $filename, $img);

          ProductVariantImage::create([
            'product_variant_id' => $product_variant->id,
            'image' => $filename,
          ]);

          if (empty($product_variant->image)) {
            $product_variant->update(['image' => $filename]);
          }
        }
      }

      return $product_variant->fresh(['images']);
    });
  }


  public function show(ProductVariant $product_variant)
  {
    return $product_variant
      ->load([
        'images',
        'product.category',
        'product.variants.color',
        'product.variants.size',
        'product.variants.material',
        'color',
        'size',
        'material',
      ])
      ->loadAvg('reviews', 'rating')
      ->loadCount('reviews');
  }


  public function delete(ProductVariant $product_variant)
  {
    return $product_variant->delete();
  }

}