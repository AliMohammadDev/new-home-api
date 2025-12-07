<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
      'rating' => $this->rating,
      'comment' => $this->comment,
      'user' => [
        'id' => $this->user->id,
        'name' => $this->user->name,
      ],
      'product_id' => $this->product_id,
    ];
  }
}
