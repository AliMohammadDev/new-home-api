<?php


namespace App\Services\Dashboard;

use App\Models\ProductImportItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductImportItemService
{
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = ProductImportItem::with(['productImport', 'productVariant'])->latest();

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
    return DB::transaction(function () use ($data) {
      $item = ProductImportItem::create($data);
      $item->productVariant()->increment('stock_quantity', $data['quantity']);
      return $item;
    });
  }

  public function show(ProductImportItem $productImportItem)
  {
    return $productImportItem;
  }

  public function update(ProductImportItem $productImportItem, array $data)
  {
    $productImportItem->update($data);
    return $productImportItem;
  }

  public function delete(ProductImportItem $productImportItem)
  {
    return $productImportItem->delete();
  }
}