<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Color>
 */
class ColorFactory extends Factory
{

  private $colors = [
    'red' => '#EF4444',
    'blue' => '#3B82F6',
    'green' => '#22C55E',
    'yellow' => '#EAB308',
    'black' => '#000000',
    'white' => '#FFFFFF',
    'gray' => '#9CA3AF',
    'fuchsia' => '#D946EF',
    'purple' => '#8B5CF6',
    'pink' => '#EC4899',
    'orange' => '#F97316',
    'brown' => '#92400E',
  ];
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $colorName = $this->faker->unique()->randomElement(array_keys($this->colors));
    return [
      'color' => $colorName,
      'hex_code' => $this->colors[$colorName],
    ];
  }
}