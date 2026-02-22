<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImportResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'supplier' => $this->supplier_name,
      'phone' => $this->supplier_phone,
      'address' => $this->address,
      'notes' => $this->notes,
      'created_at' => $this->created_at->format('Y-m-d'),
    ];
  }
}
