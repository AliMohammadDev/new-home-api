<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImport>
 */
class ProductImportFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'supplier_name' => $this->faker->company,
      'address' => $this->faker->country,
      'import_date' => $this->faker->date(),
      'quantity' => $this->faker->numberBetween(100, 500),
      'notes' => $this->faker->sentence,
    ];
  }
}
