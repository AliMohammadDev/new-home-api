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
          'image' => $this->productVariant?->image
            ? asset('storage/product_variants/' . $this->productVariant->image)
            : (
              $this->productVariant?->images?->first()
              ? asset('storage/product_variants/' . $this->productVariant->images->first()->image)
              : null
            ),
          'product_id' => $this->productVariant->product->id,
          'name' => $this->productVariant->product->translated_name,
          'price' => $this->productVariant->price,
          'discount' => $this->productVariant->discount,
          'final_price' => $this->productVariant->final_price,
          'color' => $this->productVariant->color?->color,
          'size' => $this->productVariant->size?->size,
          'material' => $this->productVariant->material?->translated_material,
        ];
      }),
      'created_at' => $this->created_at_formatted,
    ];

  }
}