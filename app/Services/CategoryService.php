<?php

namespace App\Services;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Category;

class CategoryService
{

  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = Category::query();

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
    return Category::create($data);
  }

  public function show(Category $category)
  {
    return $category;
  }

  public function update(Category $category, array $data)
  {
    $category->update($data);
    return $category;
  }

  public function delete(Category $category)
  {
    return $category->delete();
  }
}
