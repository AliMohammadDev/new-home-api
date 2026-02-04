<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingCityResource extends JsonResource
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
      'city_name' => $this->city_name,
      'estimated_delivery' => $this->estimated_delivery,
      'shipping_fee' => $this->shipping_fee,
      'is_free_shipping' => (bool) $this->is_free_shipping,
      'is_active' => (bool) $this->is_active,
    ];
  }
}
