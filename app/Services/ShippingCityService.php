<?php

namespace App\Services;

use App\Models\ShippingCity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
class ShippingCityService
{
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = ShippingCity::query();


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
    return ShippingCity::create($data);
  }
  public function show(ShippingCity $shippingCity)
  {
    return $shippingCity;
  }

  public function update(ShippingCity $shippingCity, array $data)
  {
    $shippingCity->update($data);
    return $shippingCity  ;
  }

  public function delete(ShippingCity $category)
  {
    return $category->delete();
  }
}
