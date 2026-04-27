<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductImport;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Size;
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

    $this->command->call('shield:generate', ['--all' => true]);

    $roles = [
      'super_admin' => [
        'ar' => 'مدير النظام العام',
        'en' => 'Super Admin'
      ],
      'product_data_entry' => [
        'ar' => 'مدخل بيانات المنتجات',
        'en' => 'Product Data Entry'
      ],
      'finance_manager' => [
        'ar' => 'المدير المالي',
        'en' => 'Finance Manager'
      ],
      'main_warehouse_manager' => [
        'ar' => 'مدير المستودع الرئيسي',
        'en' => 'Main Warehouse Manager'
      ],
      'sub_warehouse_manager' => [
        'ar' => 'مدير مستودع فرعي',
        'en' => 'Sub Warehouse Manager'
      ],
      'sales_point_manager' => [
        'ar' => 'مدير نقطة بيع',
        'en' => 'Sales Point Manager'
      ],
      'sales_point_cashier' => [
        'ar' => 'كاشير نقطة بيع',
        'en' => 'Sales Point Cashier'
      ],
      'delivery_company' => [
        'ar' => 'شركة توصيل',
        'en' => 'Delivery Company'
      ],
      'customer' => [
        'ar' => 'عميل',
        'en' => 'Customer'
      ],
    ];

    foreach ($roles as $name => $displayName) {
      Role::updateOrCreate(
        ['name' => $name],
        [
          'guard_name' => 'web',
          'display_name' => $displayName
        ]
      );
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