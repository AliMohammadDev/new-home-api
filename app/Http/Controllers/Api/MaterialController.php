<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Material\CreateMaterialRequest;
use App\Http\Requests\Material\UpdateMaterialRequest;
use App\Http\Resources\MaterialResource;
use App\Models\Material;
use App\Services\MaterialService;

class MaterialController extends Controller
{
  public function __construct(
    private MaterialService $materialService
  ) {
  }

  public function index()
  {
    $materials = $this->materialService->findAll();
    return MaterialResource::collection($materials);
  }

  public function store(CreateMaterialRequest $request)
  {
    $material = $this->materialService->create($request->validated());
    return new MaterialResource($material);
  }

  public function show(Material $material)
  {
    return $material;
  }

  public function update(Material $material, UpdateMaterialRequest $request)
  {
    $newMaterial = $this->materialService->update($material, $request->validated());
    return new MaterialResource($newMaterial);
  }
  public function destroy(Material $material)
  {
    $material = $this->materialService->delete($material);
    return response()->json(['message' => 'Material deleted successfully']);
  }
}
