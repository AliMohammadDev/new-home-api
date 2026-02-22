<?php

namespace App\Services\Dashboard;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\ProductImport;
class ProductImportService
{
  public function findAll(
    $paginate = false,
    $perPage = 10,
    $page = 1,
    $columns = ["*"],
  ): LengthAwarePaginator|Collection {
    $query = ProductImport::with(['variants'])->latest();

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
    return ProductImport::create($data);
  }

  public function show(ProductImport $productImport)
  {
    return $productImport;
  }

  public function update(ProductImport $productImport, array $data)
  {
    $productImport->update($data);
    return $productImport;
  }

  public function delete(ProductImport $productImport)
  {
    return $productImport->delete();
  }
}