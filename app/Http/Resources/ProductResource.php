<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
      'name' => $this->name,
      'body' => $this->body,
      'category' => [
        'id' => $this->category_id,
        'name' => $this->category?->name,
      ],
      'image' => $this->image,
      'price' => $this->price,
      'discount' => $this->discount,
      'is_featured' => $this->is_featured,
      'final_price' => $this->final_price,
    ];
  }
}
