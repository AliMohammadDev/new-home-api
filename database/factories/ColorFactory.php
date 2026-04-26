<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Color>
 */
class ColorFactory extends Factory
{

  private array $colors = [
    ['color' => ['en' => 'Stainless Steel', 'ar' => 'ستانلس ستيل'], 'hex' => '#C7C9CC'],
    ['color' => ['en' => 'Black', 'ar' => 'أسود'], 'hex' => '#111827'],
    ['color' => ['en' => 'White', 'ar' => 'أبيض'], 'hex' => '#FFFFFF'],
    ['color' => ['en' => 'Off White', 'ar' => 'أبيض مائل للصفرة'], 'hex' => '#F9FAF7'],
    ['color' => ['en' => 'Cream', 'ar' => 'كريمي'], 'hex' => '#FFF1C1'],
    ['color' => ['en' => 'Gray', 'ar' => 'رمادي'], 'hex' => '#9CA3AF'],
    ['color' => ['en' => 'Charcoal', 'ar' => 'فحمي'], 'hex' => '#374151'],
    ['color' => ['en' => 'Navy', 'ar' => 'كحلي'], 'hex' => '#1E3A8A'],
    ['color' => ['en' => 'Olive', 'ar' => 'زيتي'], 'hex' => '#6B8E23'],
    ['color' => ['en' => 'Beige', 'ar' => 'بيج'], 'hex' => '#E5D3B3'],
    ['color' => ['en' => 'Brown', 'ar' => 'بني'], 'hex' => '#8B5A2B'],
    ['color' => ['en' => 'Copper', 'ar' => 'نحاسي'], 'hex' => '#B87333'],
  ];

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $selectedColor = $this->faker->unique()->randomElement($this->colors);

    return [
      'color' => $selectedColor['color'],
      'hex_code' => $selectedColor['hex'],
    ];
  }
}
