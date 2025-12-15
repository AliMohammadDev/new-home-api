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
        'name' => $product->name,
        'image' => $product->image,
        'category' => [
          'id' => $product->category->id,
          'name' => $product->category->name,
          'image' => $product->category->image,
        ],
        'price' => $product->price,
        'discount' => $product->discount,
        'final_price' => $product->final_price,
        'reviews_avg' => $product->reviews_avg_rating ?? 0,
        'reviews_count' => $product->reviews_count ?? 0,
      ],
      'color' => $this->color->color,
      'size' => $this->size->size,
      'material' => $this->material->material,
      'stock_quantity' => $this->stock_quantity,
    ];
  }
}
