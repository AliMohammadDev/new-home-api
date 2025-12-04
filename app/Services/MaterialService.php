<?php

namespace App\Services;

use App\Http\Requests\Material\CreateMaterialRequest;
use App\Http\Requests\Material\UpdateMaterialRequest;
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
    $query = Material::query();

    if ($paginate) {
      return $query->paginate(
        perPage: $perPage,
        page: $page,
        columns: $columns,
      );
    }
    return $query->get($columns);
  }

  public function create(CreateMaterialRequest $data)
  {
    return Material::create($data->validated());
  }

  public function show(Material $material)
  {
    return $material;
  }

  public function update(Material $material, UpdateMaterialRequest $data)
  {
    $material->update($data->validated());
    return $material;
  }

  public function delete(Material $material)
  {
    return $material->delete();
  }
}
