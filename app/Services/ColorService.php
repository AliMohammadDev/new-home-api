<?php

namespace App\Services;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Color;

class ColorService
{
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = Color::with([
      'productVariants.product.media',
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
    return Color::create($data);
  }

  public function show(Color $color)
  {
    return $color;
  }

  public function update(Color $color, array $data)
  {
    $color->update($data);
    return $color;
  }

  public function delete(Color $color)
  {
    return $color->delete();
  }

}