<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Reviews;
use App\Models\Size;
use App\Models\User;
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
    User::factory(10)->create();

    Category::factory(7)->create();

    $colors = Color::factory(6)->create();
    $sizes = Size::factory(4)->create();
    $materials = Material::factory(3)->create();

    Product::factory(20)->create()->each(function ($product) use ($colors, $sizes, $materials) {
      ProductVariant::factory(rand(1, 3))->create([
        'product_id' => $product->id,
        'color_id' => $colors->random()->id,
        'size_id' => $sizes->random()->id,
        'material_id' => $materials->random()->id,
      ]);

      Reviews::factory(rand(0, 5))->create([
        'product_id' => $product->id,
      ]);
    });
  }
}