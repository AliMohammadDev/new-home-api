<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishListResource extends JsonResource
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
      'product_variant' => $this->whenLoaded('productVariant', function () {
        return [
          'id' => $this->productVariant->id,
          'product_id' => $this->productVariant->product->id,
          'name' => $this->productVariant->product->name,
          'image' => $this->productVariant->product->image,
          'price' => $this->productVariant->product->price,
          'discount' => $this->productVariant->product->discount,
          'final_price' => $this->productVariant->product->final_price,

          'color' => $this->productVariant->color?->color,
          'size' => $this->productVariant->size?->size,
          'material' => $this->productVariant->material?->material,
        ];
      }),
      'created_at' => $this->created_at_formatted,
    ];

  }
}