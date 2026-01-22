<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'name' => [
        'en' => ucfirst($this->faker->words(2, true)),
        'ar' => ucfirst($this->faker->words(2, true)),
      ],
      'body' => [
        'en' => $this->faker->paragraph(),
        'ar' => $this->faker->paragraph(),
      ],
      'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
      'is_featured' => $this->faker->boolean(20),
    ];
  }
}
