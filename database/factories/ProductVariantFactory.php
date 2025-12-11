<?php

namespace Database\Factories;

use App\Models\Color;
use App\Models\Material;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'product_id' => Product::factory(),
      'color_id' => Color::factory(),
      'size_id' => Size::factory(),
      'material_id' => Material::factory(),
      'stock_quantity' => $this->faker->numberBetween(0, 100),
    ];
  }
}
