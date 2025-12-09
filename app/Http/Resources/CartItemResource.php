<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
      'quantity' => $this->quantity,
      'product_variant' => $this->whenLoaded('productVariant', function () {
        return [
          'id' => $this->productVariant->id,
          'product_id' => $this->productVariant->product->id,
          'name' => $this->productVariant->product->name,
          'image' => $this->productVariant->product->image,
          'price' => $this->productVariant->product->price,
          'discount' => $this->productVariant->product->discount,
          'final_price' => $this->productVariant->product->final_price,
          'color' => $this->productVariant->color->name,
          'size' => $this->productVariant->size->name,
          'material' => $this->productVariant->material->name,
        ];
      }),
      'total_price' => $this->productVariant->product->final_price * $this->quantity,
    ];

  }
}
