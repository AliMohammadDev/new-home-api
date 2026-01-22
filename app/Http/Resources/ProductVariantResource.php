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
      'image' => $this->image
        ? asset('storage/product_variants/' . $this->id . '/' . $this->image)
        : ($this->images->first()
          ? asset('storage/product_variants/' . $this->id . '/' . $this->images->first()->image)
          : null),

      'product_all_images' => $this->images->map(function ($img) {
        return asset('storage/product_variants/' . $this->id . '/' . $img->image);
      }),

      'product' => $this->whenLoaded('product', function () {
        // all images
  
        return [
          'id' => $this->product->id,
          'name' => $this->product->translated_name,
          'body' => $this->product->translated_body,
          // category
          'category' => $this->product->category ? [
            'id' => $this->product->category->id,
            'name' => $this->product->category->translated_name,
            'description' => $this->product->category->translated_description,
            'image' => $this->product->category->getFirstMediaUrl('category_images', 'default'),
          ] : null,
          'available_options' => $this->product->variants->groupBy('color_id')->map(function ($colorGroup) {
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

                      'reviews_avg' => $variant->reviews_avg_rating,
                      'reviews_count' => $variant->reviews_count,
                    ];
                  })->values(),
                ];
              })->values(),
            ];
          })->values(),
        ];
      }),

      'price' => $this->price,
      'discount' => $this->discount,
      'final_price' => $this->final_price,
      'current_color' => $this->color ? $this->color->color : null,
      'current_size' => $this->size ? $this->size->size : null,
      'current_material' => $this->material ? $this->material->translated_material : null,
      'stock_quantity' => $this->stock_quantity,
      'sku' => $this->sku,
      'reviews_avg' => $this->reviews_avg_rating,
      'reviews_count' => $this->reviews_count,
    ];
  }
}
