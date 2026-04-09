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
use Mpdf\Tag\Del;
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

    $roles = [
      'super_admin',
      'product_data_entry',
      'finance_manager',
      'main_warehouse_manager',
      'sub_warehouse_manager',
      'sales_point_manager',
      'sales_point_cashier',
      'delivery_company',
      'customer'
    ];

    foreach ($roles as $role) {
      Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }


    $this->call([
      AdminUserSeeder::class,
      CompanyTreasureSeeder::class,
      UserSeeder::class,
      CategorySeeder::class,
      ProductSeeder::class,
      WarehouseSeeder::class,
      ShippingCitySeeder::class,
      SalesPointSeeder::class,
      CashierSeeder::class,
      DeliveryCompanySeeder::class,
    ]);


    $allImports = ProductImport::factory(10)->create();

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
