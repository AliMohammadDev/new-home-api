<?php

namespace App\Services;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Category;
use Cloudinary\Cloudinary;


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

  public function create(array $data, $imageFile = null)
  {
    if ($imageFile) {
      $cloudinary = new Cloudinary(config('cloudinary.url'));
      $uploaded = $cloudinary->uploadApi()->upload(
        $imageFile->getRealPath(),
        ['folder' => 'categories']
      );
      $data['image'] = $uploaded['secure_url'];
      $data['image_public_id'] = $uploaded['public_id'];
    }
    return Category::create($data);
  }
  public function show(Category $category)
  {
    return $category;
  }

  public function update(Category $category, array $data, $imageFile = null)
  {
    if ($imageFile) {
      $cloudinary = new Cloudinary(config('cloudinary.url'));
      if ($category->image_public_id) {
        $cloudinary->uploadApi()->destroy($category->image_public_id);
      }
      $uploaded = $cloudinary->uploadApi()->upload(
        $imageFile->getRealPath(),
        ['folder' => 'categories']
      );
      $data['image'] = $uploaded['secure_url'];
      $data['image_public_id'] = $uploaded['public_id'];
    }
    $category->update($data);
    return $category;
  }

  public function delete(Category $category)
  {
    return $category->delete();
  }
}