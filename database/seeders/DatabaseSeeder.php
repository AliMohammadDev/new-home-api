<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Reviews;
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
    $this->call(CategorySeeder::class);

    $colors = Color::factory(6)->create();
    $sizes = Size::factory(4)->create();
    $materials = Material::factory(3)->create();
    $products = Product::factory(10)->create();

    for ($i = 0; $i < 15; $i++) {
      $variant = ProductVariant::firstOrCreate([
        'product_id' => $products->random()->id,
        'color_id' => $colors->random()->id,
        'size_id' => $sizes->random()->id,
        'material_id' => $materials->random()->id,
      ], [
        'price' => rand(10, 100),
        'discount' => rand(0, 30),
        'stock_quantity' => rand(5, 50),
        'sku' => 'PROD-' . strtoupper(Str::random(8)),
      ]);

      $numberOfReviews = rand(0, 2);

      if ($numberOfReviews > 0) {
        $randomUsers = $users->random($numberOfReviews);
        foreach ($randomUsers as $user) {
          Reviews::updateOrCreate(
            [
              'user_id' => $user->id,
              'product_variant_id' => $variant->id,
            ],
            [
              'rating' => rand(1, 5),
              'comment' => 'This is a seed comment.',
              'created_at' => now(),
              'updated_at' => now(),
            ]
          );
        }
      }
    }
  }

}
