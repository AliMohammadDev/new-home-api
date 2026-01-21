<?php


namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\ProductVariantPackage;

class ProductVariantPackageService
{


  public function getByVariant(ProductVariant $productVariant)
  {
    return ProductVariantPackage::where(
      'product_variant_id',
      $productVariant->id
    )
      ->orderBy('quantity')
      ->get();
  }
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = ProductVariantPackage::with('variant');

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
    return ProductVariantPackage::create($data);
  }

  public function show(ProductVariantPackage $newPackage)
  {
    return $newPackage;
  }

  public function update(ProductVariantPackage $newPackage, array $data)
  {
    $newPackage->update($data);
    return $newPackage;
  }

  public function delete(ProductVariantPackage $newPackage)
  {
    return $newPackage->delete();
  }
}