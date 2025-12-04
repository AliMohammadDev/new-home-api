<?php

namespace App\Services;

use App\Http\Requests\Size\CreateSizeRequest;
use App\Http\Requests\Size\UpdateSizeRequest;
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
    $query = Size::query();

    if ($paginate) {
      return $query->paginate(
        perPage: $perPage,
        page: $page,
        columns: $columns,
      );
    }
    return $query->get($columns);
  }

  public function create(CreateSizeRequest $data)
  {
    return Size::create($data->validated());
  }

  public function show(Size $size)
  {
    return $size;
  }

  public function update(Size $size, UpdateSizeRequest $data)
  {
    $size->update($data->validated());
    return $size;
  }

  public function delete(Size $size)
  {
    return $size->delete();
  }
}
