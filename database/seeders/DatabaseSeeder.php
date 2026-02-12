<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductImport;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Seed the application's database.
   */

  public function run(): void
  {

    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

    $users = User::factory(10)->create();
    Warehouse::factory(6)->create();
    $this->call(CategorySeeder::class);
    $this->call(ProductSeeder::class);
    $this->call(ShippingCitySeeder::class);


    $allImports = ProductImport::factory(5)->create();

    $colors = Color::factory(6)->create();
    $sizes = Size::factory(4)->create();
    $materials = Material::factory(3)->create();

    $products = Product::all();

    foreach ($products as $product) {
      $combinations = [];

      for ($i = 0; $i < 4; $i++) {
        $c_id = $colors->random()->id;
        $s_id = $sizes->random()->id;
        $m_id = $materials->random()->id;

        $key = "{$c_id}-{$s_id}-{$m_id}";
        if (in_array($key, $combinations)) {
          continue;
        }
        $combinations[] = $key;

        $randomImport = $allImports->random();

        ProductVariant::factory()->create([
          'product_id' => $product->id,
          'color_id' => $c_id,
          'size_id' => $s_id,
          'material_id' => $m_id,
          'product_import_id' => $randomImport->id,
          'stock_quantity' => $randomImport->quantity,

        ]);
      }
    }
  }
}
