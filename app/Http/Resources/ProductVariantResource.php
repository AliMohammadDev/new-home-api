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
    $product = $this->product;

    return [
      'id' => $this->id,
      'product' => [
        'id' => $product->id,
        'name' => $product->translated_name,
        'image' => $product->getFirstMediaUrl('product_images', 'default'),
        'category' => [
          'id' => $product->category->id,
          'name' => $product->category->translated_name,
          'image' => $product->category->getFirstMediaUrl('category_images', 'default'),
        ],
        'price' => $product->price,
        'discount' => $product->discount,
        'final_price' => $product->final_price,

      ],
      'color' => [
        'name' => $this->color->color,
        'hex_code' => $this->color->hex_code,
      ],

      'size' => $this->size->size,
      'material' => $this->material->material,
      'stock_quantity' => $this->stock_quantity,
      'reviews_avg' => $this->reviews_avg_rating,
      'reviews_count' => $this->reviews_count,
    ];
  }
}