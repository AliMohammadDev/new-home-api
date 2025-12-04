<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Size\CreateSizeRequest;
use App\Http\Requests\Size\UpdateSizeRequest;
use App\Http\Resources\SizeResource;
use App\Models\Size;
use App\Services\SizeService;

class SizeController extends Controller
{
  public function __construct(
    private SizeService $sizeService
  ) {
  }

  public function index()
  {
    $sizes = $this->sizeService->findAll();
    return SizeResource::collection($sizes);
  }

  public function store(CreateSizeRequest $data)
  {
    $size = $this->sizeService->create($data);
    return new SizeResource($size);
  }

  public function show(Size $size)
  {
    return $size;
  }

  public function update(Size $size, UpdateSizeRequest $data)
  {
    $newSize = $this->sizeService->update($size, $data);
    return new SizeResource($newSize);
  }
  public function destroy(Size $size)
  {
    $size = $this->sizeService->delete($size);
    return response()->json(['message' => 'Size deleted successfully']);
  }
}
