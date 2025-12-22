<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reviews>
 */
class ReviewsFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'user_id' => User::factory(),
      'product_variant_id' => ProductVariant::inRandomOrder()->first()?->id ?? ProductVariant::factory(),
      'rating' => $this->faker->numberBetween(1, 5),
      'comment' => $this->faker->sentence(),
    ];
  }
}
