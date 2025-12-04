<?php

namespace App\Services;

use App\Http\Requests\Color\CreateColorRequest;
use App\Http\Requests\Color\UpdateColorRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Color;
use App\Models\Product;

class ColorService
{
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = Color::query();

    if ($paginate) {
      return $query->paginate(
        perPage: $perPage,
        page: $page,
        columns: $columns,
      );
    }
    return $query->get($columns);
  }

  public function create(CreateColorRequest $data)
  {
    return Color::create($data->validated());
  }

  public function show(Color $color)
  {
    return $color;
  }

  public function update(Color $color, UpdateColorRequest $data)
  {
    $color->update($data->validated());
    return $color;
  }

  public function delete(Color $color)
  {
    return $color->delete();
  }

 

}
