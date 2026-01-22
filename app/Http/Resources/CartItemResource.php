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
      'image' => $this->productVariant?->image
        ? asset('storage/product_variants/' . $this->productVariant->id . '/' . $this->productVariant->image)
        : ($this->productVariant?->images?->first()
          ? asset('storage/product_variants/' . $this->productVariant->id . '/' . $this->productVariant->images->first()->image)
          : null),

      'quantity' => $this->quantity,
      'product_variant' => $this->whenLoaded('productVariant', function () {
        return [
          'id' => $this->productVariant->id,
          'product_id' => $this->productVariant->product->id,
          'name' => $this->productVariant->product->translated_name,
          'price' => $this->productVariant->price,
          'discount' => $this->productVariant->discount,
          'sku' => $this->productVariant->sku,
          'final_price' => $this->productVariant->final_price,
          'color_name' => $this->productVariant->color->color,
          'color_code' => $this->productVariant->color->hex_code,
          'size' => $this->productVariant->size->size,
          'material' => $this->productVariant->material->translated_material,
        ];
      }),

      'available_options' => $this->productVariant->product->variants->groupBy('color_id')->map(function ($colorGroup) {
        $color = $colorGroup->first()->color;
        return [
          'id' => $color->id,
          'name' => $color->color,
          'hex' => $color->hex_code,
          'available_sizes' => $colorGroup->groupBy('size_id')->map(function ($sizeGroup) {
            $size = $sizeGroup->first()->size;
            return [
              'id' => $size->id,
              'name' => $size->size,
              'available_materials' => $sizeGroup->map(function ($variant) {
                $material = $variant->material;
                return [
                  'id' => $material->id,
                  'name' => $material->translated_material,
                  'stock' => $variant->stock_quantity,
                  'variant_id' => $variant->id,
                  'price' => $variant->price,
                  'final_price' => $variant->final_price,
                  'sku' => $variant->sku,
                ];
              })->values(),
            ];
          })->values(),
        ];
      })->values()
      ,
      'total_price' => round(
        $this->productVariant->final_price * $this->quantity,
        2
      ),
    ];
  }
}
