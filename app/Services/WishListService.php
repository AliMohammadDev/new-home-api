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
    $query = WishList::with([
      'productVariant.product',
      'productVariant.images',
      'productVariant.color',
      'productVariant.size',
      'productVariant.material',
    ]);

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

  public function deleteAll($userId)
  {
    return WishList::where('user_id', $userId)->delete();
  }

  public function toggle($userId, $productVariantId)
  {
    $exists = WishList::where('user_id', $userId)
      ->where('product_variant_id', $productVariantId)
      ->first();

    if ($exists) {
      $exists->delete();
      return ['status' => 'removed'];
    }

    $new = WishList::create([
      'user_id' => $userId,
      'product_variant_id' => $productVariantId
    ]);
    return ['status' => 'added', 'data' => $new];
  }
}