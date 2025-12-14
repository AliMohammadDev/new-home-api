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
  ) {
  }

  public function index()
  {
    $categories = $this->categoryService->findAll();
    return CategoryResource::collection($categories);
  }

  public function store(CreateCategoryRequest $request)
  {

    $validated = $request->validated();
    $category = $this->categoryService->create(
      $validated,
      $request->file('image')
    );
    return new CategoryResource($category);
  }

  public function show(Category $category)
  {
    return $category;
  }

  public function update(Category $category, UpdateCategoryRequest $request)
  {
    $validated = $request->validated();
    $newCategory = $this->categoryService->update(
      $category,
      $validated,
      $request->file('image')
    );
    return new CategoryResource($newCategory);
  }
  public function destroy(Category $category)
  {
    $category = $this->categoryService->delete($category);
    return response()->json(['message' => 'Category deleted successfully']);
  }
}