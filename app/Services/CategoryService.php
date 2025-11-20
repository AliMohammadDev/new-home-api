<?php

namespace App\Services;

use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Requests\Color\UpdateColorRequest;
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

  public function create(CreateCategoryRequest $data)
  {
    return Category::create($data->validated());
  }

  public function show(Category $category)
  {
    return $category;
  }

  public function update(Category $category, UpdateCategoryRequest $data)
  {
    $category->update($data->validated());
    return $category;
  }

  public function delete(Category $category)
  {
    return $category->delete();
  }
}
