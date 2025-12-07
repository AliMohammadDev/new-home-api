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
      'quantity' => $this->quantity,
      'product' => $this->whenLoaded('product', function () {
        return [
          'id' => $this->product->id,
          'name' => $this->product->name,
          'image' => $this->product->image,
          'price' => $this->product->price,
          'discount' => $this->product->discount,
          'final_price' => $this->product->final_price,
        ];
      }),
      'total_price' => $this->product->final_price * $this->quantity,
      'created_at' => $this->created_at,
    ];
  }
}
