<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Seed the application's database.
   */

  public function run(): void
  {
    $users = User::factory(10)->create();

    // Categories
    $this->call(CategorySeeder::class);

    // Products (real content)
    $this->call(ProductSeeder::class);

    $colors = Color::factory(6)->create();
    $sizes = Size::factory(4)->create();
    $materials = Material::factory(3)->create();

    $products = Product::all();


    foreach ($products as $product) {
      for ($i = 0; $i < 2; $i++) {
        $color = $colors->random();
        $size = $sizes->random();
        $material = $materials->random();

        ProductVariant::firstOrCreate(
          [
            'product_id' => $product->id,
            'color_id' => $color->id,
            'size_id' => $size->id,
            'material_id' => $material->id,
          ],
          [
            'price' => rand(30, 200),
            'discount' => rand(0, 25),
            'stock_quantity' => rand(10, 60),
            'sku' => 'PROD-' . strtoupper(Str::random(8)),
          ]
        );
      }
    }




  }

}
