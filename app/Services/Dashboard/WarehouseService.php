<?php

namespace App\Services\Dashboard;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
class WarehouseService
{
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = Warehouse::query();


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
    return Warehouse::create($data);
  }
  public function show(Warehouse $warehouse)
  {
    return $warehouse;
  }

  public function update(Warehouse $warehouse, array $data)
  {
    $warehouse->update($data);
    return $warehouse;
  }

  public function delete(Warehouse $warehouse)
  {
    return $warehouse->delete();
  }
}
