<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
      'total_amount' => $this->total_amount,
      'shipping_fee' => $this->shipping_fee,
      'subtotal' => $this->total_amount - $this->shipping_fee,
      'payment_method' => $this->payment_method,
      'status' => $this->status,
      'created_at' => $this->created_at_formatted,
      'checkout' => new CheckoutResource($this->whenLoaded('checkout')),
      'items' => OrderItemResource::collection(
        $this->whenLoaded('orderItems')
      ),
    ];
  }
}
