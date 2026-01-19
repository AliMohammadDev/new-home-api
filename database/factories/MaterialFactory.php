<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $materials = [
      ['en' => 'Ceramic', 'ar' => 'سيراميك'],
      ['en' => 'Glass', 'ar' => 'زجاج'],
      ['en' => 'Stainless Steel', 'ar' => 'ستانلس ستيل'],
      ['en' => 'Cast Iron', 'ar' => 'حديد صلب'],
      ['en' => 'Plastic', 'ar' => 'بلاستيك'],
      ['en' => 'Wood', 'ar' => 'خشب'],
    ];

    return [
      'material' => $this->faker->unique()->randomElement($materials),
    ];
  }
}