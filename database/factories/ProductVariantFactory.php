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

    $price = $this->faker->randomFloat(2, 0.2, 100);

    return [
      'product_id' => Product::factory(),
      'color_id' => Color::factory(),
      'size_id' => Size::factory(),
      'material_id' => Material::factory(),

      'price' => $price,
      'discount' => $this->faker->numberBetween(0, 50),

      'stock_quantity' => $this->faker->numberBetween(0, 100),
      'sku' => 'PROD-' . strtoupper($this->faker->unique()->bothify('??###-??')),
    ];
  }
}
