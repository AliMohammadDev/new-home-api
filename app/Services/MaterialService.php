<?php

namespace App\Services;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Material;

class MaterialService
{

  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = Material::with([
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
    return Material::create($data);
  }

  public function show(Material $material)
  {
    return $material;
  }

  public function update(Material $material, array $data)
  {
    $material->update($data);
    return $material;
  }

  public function delete(Material $material)
  {
    return $material->delete();
  }
}