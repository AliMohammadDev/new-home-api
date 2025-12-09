<?php
namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductVariantService
{
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = ProductVariant::with(['product', 'color', 'size', 'material']);

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
    return $product_variant->load(['product', 'color', 'size', 'material']);
  }

  public function delete(ProductVariant $product_variant)
  {
    return $product_variant->delete();
  }

}
