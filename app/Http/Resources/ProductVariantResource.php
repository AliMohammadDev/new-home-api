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

      'product' => $this->whenLoaded('product', function () {
        return [
          'id' => $this->product->id,
          'name' => $this->product->translated_name,
          'image' => $this->product->getFirstMediaUrl('product_images', 'default'),
          'body' => $this->product->translated_body,
          'category' => $this->product->category ? [
            'id' => $this->product->category->id,
            'name' => $this->product->category->translated_name,
            'description' => $this->product->category->translated_description,
            'image' => $this->product->category->getFirstMediaUrl('category_images', 'default'),
          ] : null,

          'price' => $this->product->price,
          'discount' => $this->product->discount,
          'final_price' => $this->product->final_price,
        ];
      }),
      'color' => $this->whenLoaded('color', fn() => [
        'name' => $this->color->color,
        'hex_code' => $this->color->hex_code,
      ]),
      'size' => $this->whenLoaded('size', fn() => $this->size->size),
      'material' => $this->whenLoaded('material', fn() => $this->material->material),
      'stock_quantity' => $this->stock_quantity,
      'reviews_avg' => $this->reviews_avg_rating,
      'reviews_count' => $this->reviews_count,
    ];
  }

}
