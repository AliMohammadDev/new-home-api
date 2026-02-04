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
    $query = Category::with([
      'media',
      'products',
      'products.variants' => function ($q) {
        $q->with([
          'color',
          'size',
          'material',
          'images',
          'packages',
          'product.variants.color'
        ])
          ->withAvg('reviews', 'rating')
          ->withCount('reviews');
      },
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

  public function create(array $data, $imageFile = null)
  {
    return Category::create($data);
  }
  public function show(Category $category)
  {
    return $category;
  }

  public function update(Category $category, array $data, $imageFile = null)
  {
    $category->update($data);
    return $category;
  }

  public function delete(Category $category)
  {
    return $category->delete();
  }
}
