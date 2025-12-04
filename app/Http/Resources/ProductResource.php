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
      'body' => $this->body,
      'category_id' => $this->category_id,
      'category_name' => $this->category?->name,
      'image' => $this->image ? asset('storage/' . $this->image) : null,
      'price' => $this->price,
      'discount' => $this->discount,
    ];
  }
}
