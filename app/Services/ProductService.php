<?php


namespace App\Services;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;


class ProductService
{

  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = Product::with([
      'category.media',
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
    return Product::create($data);
  }

  public function show(Product $product)
  {
    return $product;
  }

  public function update(Product $product, array $data, $imageFile = null)
  {
    $product->update($data);
    return $product;
  }

  public function delete(Product $product)
  {
    return $product->delete();
  }


}