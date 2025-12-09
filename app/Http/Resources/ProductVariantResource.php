<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
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
      'product' => [
        'id' => $this->product->id,
        'name' => $this->product->name,
        'image' => $this->product->image,
        'price' => $this->product->price,
        'discount' => $this->product->discount,
        'final_price' => $this->product->final_price,
      ],
      'color' => $this->color->color,
      'size' => $this->size->size,
      'material' => $this->material->material,
      'stock_quantity' => $this->stock_quantity,
    ];
  }
}
