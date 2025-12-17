<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Reviews;


class ReviewsService
{

  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
    $userId = null
  ): LengthAwarePaginator|Collection {
    $query = Reviews::with(['ProductVariant', 'user']);
    if ($userId) {
      $query->where('user_id', $userId);
    }
    if ($paginate) {
      return $query->paginate(
        perPage: $perPage,
        page: $page,
        columns: $columns,
      );
    }
    return $query->get($columns);
  }
  public function create(array $data, $userId)
  {
    return Reviews::updateOrCreate(
      [
        'user_id' => $userId,
        'product_variant_id' => $data['product_variant_id'],
      ],
      [
        'rating' => $data['rating'],
        'comment' => $data['comment'] ?? null
      ]
    );
  }

  public function update(Reviews $review, array $data)
  {
    $review->update($data);
    return $review;
  }

  public function delete(Reviews $review)
  {
    return $review->delete();
  }
}