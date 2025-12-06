<?php


namespace App\Services;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\WishList;

class WishListService
{
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
    $userId = null
  ): LengthAwarePaginator|Collection {
    $query = WishList::query();
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

  public function create(array $data)
  {
    return WishList::create($data);
  }

  public function delete(WishList $wishlist)
  {
    return $wishlist->delete();
  }
}
