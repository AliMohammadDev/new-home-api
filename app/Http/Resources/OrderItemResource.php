<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
      'price' => $this->price,
      'total' => $this->total,
      'product_variant' => $this->whenLoaded('productVariant', function () {
        return [
          'id' => $this->productVariant->id,
          'product_id' => $this->productVariant->product->id,

          'image' => $this->productVariant?->image
            ? asset('storage/product_variants/' . $this->productVariant->image)
            : (
              $this->productVariant?->images?->first()
              ? asset('storage/product_variants/' . $this->productVariant->images->first()->image)
              : null
            ),
          'name' => $this->productVariant->product->translated_name,
          'price' => $this->productVariant->price,
          'discount' => $this->productVariant->discount,
          'final_price' => $this->productVariant->final_price,
          'color' => $this->productVariant->color->color,
          'size' => $this->productVariant->size->size,
          'material' => $this->productVariant->material->translated_material,
        ];
      }),
    ];
  }
}
