<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
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
      'color' => $this->color,
      'hex_code' => $this->hex_code,
      'productVariants' => ProductVariantResource::collection($this->whenLoaded('productVariants')),
    ];
  }
}