<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Color\CreateColorRequest;
use App\Http\Requests\Color\UpdateColorRequest;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use App\Services\ColorService;

class ColorController extends Controller
{
  public function __construct(
    private ColorService $colorService
  ) {
  }

  public function index()
  {
    $categories = $this->colorService->findAll();
    return ColorResource::collection($categories);
  }

  public function store(CreateColorRequest $request)
  {
    $color = $this->colorService->create($request->validated());
    return new ColorResource($color);
  }

  public function show(Color $color)
  {
    return $color;
  }

  public function update(Color $color, UpdateColorRequest $request)
  {
    $newColor = $this->colorService->update($color, $request->validated());
    return new ColorResource($newColor);
  }
  public function destroy(Color $color)
  {
    $color = $this->colorService->delete($color);
    return response()->json(['message' => 'Color deleted successfully']);
  }



}
