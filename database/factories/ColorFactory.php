<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Color>
 */
class ColorFactory extends Factory
{

  private $colors = [
    'stainless_steel' => '#C7C9CC',
    'black' => '#111827',
    'white' => '#FFFFFF',
    'off_white' => '#F9FAF7',
    'cream' => '#FFF1C1',
    'gray' => '#9CA3AF',
    'charcoal' => '#374151',
    'navy' => '#1E3A8A',
    'olive' => '#6B8E23',
    'beige' => '#E5D3B3',
    'brown' => '#8B5A2B',
    'copper' => '#B87333',
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
