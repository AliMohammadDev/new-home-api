<?php

namespace App\Services;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Size;

class SizeService
{
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = Size::with([
      'productVariants.product',
      'productVariants.color',
      'productVariants.size',
      'productVariants.material',
      'productVariants.reviews'
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

  public function create(array $data)
  {
    return Size::create($data);
  }

  public function show(Size $size)
  {
    return $size;
  }

  public function update(Size $size, array $data)
  {
    $size->update($data);
    return $size;
  }

  public function delete(Size $size)
  {
    return $size->delete();
  }
}