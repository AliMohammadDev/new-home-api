<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Models\Category;

class CategoryController extends Controller
{
  public function __construct(
    private CategoryService $categoryService
  ) {}

  public function index()
  {
    $categories = $this->categoryService->findAll();
    return CategoryResource::collection($categories);
  }

  public function store(CreateCategoryRequest $data)
  {
    $category =  $this->categoryService->create($data);
    return new CategoryResource($category);
  }

  public function update(Category $category, UpdateCategoryRequest $data)
  {
    $newCategory = $this->categoryService->update($category, $data);
    return new CategoryResource($newCategory);
  }
  public function destroy(Category $category)
  {
    $category = $this->categoryService->delete($category);
    return response()->json(['message' => 'Category deleted successfully']);
  }
}
