<?php

namespace App\Services\Dashboard;

use App\Models\ShippingWarehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
class ShippingWarehouseService
{

  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = ShippingWarehouse::query();


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
    return ShippingWarehouse::create($data);
  }
  public function show(ShippingWarehouse $shippingWarehouse)
  {
    return $shippingWarehouse;
  }

  public function update(ShippingWarehouse $shippingWarehouse, array $data)
  {
    $shippingWarehouse->update($data);
    return $shippingWarehouse;
  }

  public function delete(ShippingWarehouse $shippingWarehouse)
  {
    return $shippingWarehouse->delete();
  }
}