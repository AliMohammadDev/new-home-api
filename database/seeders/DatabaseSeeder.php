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
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Seed the application's database.
   */

  public function run(): void
  {

    $this->command->call('shield:generate', ['--all' => true]);

    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

    User::factory(10)->create();

    Warehouse::factory(6)->create();
    $this->call(CategorySeeder::class);
    $this->call(ProductSeeder::class);
    $this->call(ShippingCitySeeder::class);
    $this->call(
      AdminUserSeeder::class,
    );

    $this->call([
      SalesPointSeeder::class,
    ]);
    $this->call(CashierSeeder::class);
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

        $randomQuantity = rand(50, 200);

        ProductVariant::factory()->create([
          'product_id' => $product->id,
          'color_id' => $c_id,
          'size_id' => $s_id,
          'material_id' => $m_id,
          'stock_quantity' => $randomQuantity,
        ]);
      }
    }
  }
}
