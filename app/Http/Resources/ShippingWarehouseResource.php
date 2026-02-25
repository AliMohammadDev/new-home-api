<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingWarehouseResource extends JsonResource
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
      'amount' => $this->amount,
      'unit' => $this->unit_name . ' (' . $this->unit_capacity . ')',
      'arrival_time' => $this->arrival_time,
      'warehouse' => new WarehouseResource($this->whenLoaded('warehouse')),
      'product' => $this->whenLoaded('productVariant'),
    ];
  }
}