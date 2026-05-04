<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\ProductVariantImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ProductVariantService
{


  public function findVariantsByCategoryName(int $categoryId)
  {
    return ProductVariant::query()
      ->with([
        'product' => fn($q) => $q->select('id', 'name', 'category_id')
          ->with(['category:id,name,description']),
        'color:id,color',
        'size:id,size',
        'material:id,material',
        'images'
      ])
      ->whereHas('product.category', function ($q) use ($categoryId) {
        $q->where('id', $categoryId);
      })
      ->where('stock_quantity', '>', 0)
      ->withAvg('reviews', 'rating')
      ->withCount('reviews')
      ->whereIn('id', function ($q) {
        $q->selectRaw('MIN(id)')
          ->from('product_variants')
          ->where('stock_quantity', '>', 0)
          ->groupBy('product_id');
      })
      ->get();
  }

  public function getSlidersProductsVariants(int $limit = 10)
  {
    $baseQuery = ProductVariant::query()
      ->with([
        'product' => fn($q) => $q->select('id', 'name', 'category_id', 'is_featured', 'created_at')
          ->with(['category:id,name']),
        'color:id,color',
        'size:id,size',
        'material:id,material',
      ])
      ->withAvg('reviews', 'rating')
      ->withCount('reviews')
      ->where('stock_quantity', '>', 0)
      ->whereIn('id', function ($q) {
        $q->selectRaw('MIN(id)')
          ->from('product_variants')
          ->where('stock_quantity', '>', 0)
          ->groupBy('product_id');
      });

    return cache()->remember('home_sliders_variants', 3600, function () use ($baseQuery, $limit) {

      return [
        'featured' => (clone $baseQuery)
          ->whereHas('product', fn($q) => $q->where('is_featured', true))
          ->latest()
          ->take($limit)
          ->get(),

        'new' => (clone $baseQuery)
          ->whereHas('product', fn($q) => $q->where('created_at', '>=', now()->subDays(30)))
          ->latest()
          ->take($limit)
          ->get(),

        'discounted' => (clone $baseQuery)
          ->whereHas('product', fn($q) => $q->where('discount', '>', 0))
          ->orderByRaw('(select discount from products where products.id = product_variants.product_id) DESC')
          ->take($limit)
          ->get(),

        'top_rated' => (clone $baseQuery)
          ->having('reviews_avg_rating', '>', 0)
          ->orderByDesc('reviews_avg_rating')
          ->take($limit)
          ->get(),
      ];
    });
  }

  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = ProductVariant::query()
      ->with([
        'product' => fn($q) => $q
          ->select('id', 'category_id', 'name', 'created_at', 'is_featured')
          ->with('category:id,name'),
        'color:id,color',
        'size:id,size',
        'material:id,material',
      ])
      ->where('stock_quantity', '>', 0)
      ->withAvg('reviews', 'rating')
      ->withCount('reviews')
      ->whereIn('id', function ($q) {
        $q->selectRaw('MIN(id)')
          ->from('product_variants')
          ->where('stock_quantity', '>', 0)
          ->groupBy('product_id');
      });

    $query->latest();

    if ($paginate) {
      return $query->paginate(
        perPage: $perPage,
        columns: $columns,
        pageName: 'page',
        page: $page,
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
      if (isset($data['packages']) && is_array($data['packages'])) {
        foreach ($data['packages'] as $packageData) {
          $variant->packages()->create([
            'quantity' => $packageData['quantity'],
            'price' => $packageData['price'],
          ]);
        }
      }
      if (isset($data['images']) && is_array($data['images'])) {
        $variantDirectory = "product_variants/{$variant->id}";
        if (!Storage::disk('public')->exists($variantDirectory)) {
          Storage::disk('public')->makeDirectory($variantDirectory);
        }
        foreach ($data['images'] as $index => $imageFile) {
          $filename = Str::uuid() . '.webp';
          $finalPath = "{$variantDirectory}/{$filename}";

          $img = Image::make($imageFile)
            ->resize(1000, 1000, function ($constraint) {
              $constraint->aspectRatio();
              $constraint->upsize();
            })
            ->encode('webp', 70);
          Storage::disk('public')->put($finalPath, $img);
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

      if (isset($data['packages']) && is_array($data['packages'])) {
        $product_variant->packages()->delete();
        foreach ($data['packages'] as $packageData) {
          $product_variant->packages()->create([
            'quantity' => $packageData['quantity'],
            'price' => $packageData['price'],
          ]);
        }
      }

      if (isset($data['images']) && is_array($data['images'])) {
        $variantDirectory = "product_variants/{$product_variant->id}";

        if (!Storage::disk('public')->exists($variantDirectory)) {
          Storage::disk('public')->makeDirectory($variantDirectory);
        }

        foreach ($data['images'] as $index => $imageFile) {
          $filename = (string) Str::uuid() . '.webp';
          $finalPath = "{$variantDirectory}/{$filename}";

          $img = Image::make($imageFile)
            ->resize(1000, 1000, function ($constraint) {
              $constraint->aspectRatio();
              $constraint->upsize();
            })
            ->encode('webp', 70);

          Storage::disk('public')->put($finalPath, $img);

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
        'color',
        'size',
        'material',
        'product.category',
        'product.variants' => function ($query) {
          $query->select('id', 'product_id', 'color_id', 'size_id', 'material_id', 'price', 'stock_quantity')
            ->where('stock_quantity', '>', 0)
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->with(['color', 'size', 'material']);
        },
      ])
      ->loadAvg('reviews', 'rating')
      ->loadCount('reviews');
  }


  public function delete(ProductVariant $product_variant)
  {
    return $product_variant->delete();
  }
}