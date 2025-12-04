<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Material\CreateMaterialRequest;
use App\Http\Requests\Material\UpdateMaterialRequest;
use App\Http\Resources\MaterialResource;
use App\Models\Material;
use App\Services\MaterialService;
use Illuminate\Http\Request;

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

  public function store(CreateMaterialRequest $data)
  {
    $material = $this->materialService->create($data);
    return new MaterialResource($material);
  }

  public function show(Material $material)
  {
    return $material;
  }

  public function update(Material $material, UpdateMaterialRequest $data)
  {
    $newMaterial = $this->materialService->update($material, $data);
    return new MaterialResource($newMaterial);
  }
  public function destroy(Material $material)
  {
    $material = $this->materialService->delete($material);
    return response()->json(['message' => 'Material deleted successfully']);
  }
}
