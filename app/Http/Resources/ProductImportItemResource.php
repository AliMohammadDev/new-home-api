<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImportItemResource extends JsonResource
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
      'variant' => new ProductVariantResource($this->whenLoaded('productVariant')),
      'quantity' => $this->quantity,
      'unit_price' => $this->price,
      'shipping' => $this->shipping_price,
      'expected_arrival' => $this->expected_arrival ? $this->expected_arrival->format('Y-m-d H:i') : null,
      'import_info' => [
        'supplier' => $this->productImport->supplier_name ?? null,
        'phone' => $this->productImport->supplier_phone ?? null,
        'address' => $this->productImport->address ?? null,
        'notes' => $this->productImport->notes ?? null,
      ],
      'total_cost' => ($this->price + $this->shipping_price) * $this->quantity,
    ];
  }
}
