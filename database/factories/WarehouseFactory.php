<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {

    $warehouses = [
      ['name' => 'مستودع الفيحاء المركزي', 'city' => 'دمشق', 'address' => 'المنطقة الصناعية - ركن الدين'],
      ['name' => 'مستودع الشهباء الرئيسي', 'city' => 'حلب', 'address' => 'الليرمون - حي الصناعة'],
      ['name' => 'مستودع الخليج اللوجستي', 'city' => 'دبي', 'address' => 'جبل علي - المنطقة الحرة'],
      ['name' => 'مستودع النيل للتوريدات', 'city' => 'القاهرة', 'address' => 'مدينة السادس من أكتوبر'],
      ['name' => 'مستودع المتوسط الشامل', 'city' => 'اللاذقية', 'address' => 'قرب المرفأ - المنطقة الحرة'],
      ['name' => 'مستودع الرياض المتطور', 'city' => 'الرياض', 'address' => 'حي السلي - طريق الخرج'],
    ];

    $warehouse = $this->faker->randomElement($warehouses);

    return [
      'name' => $warehouse['name'],
      'city' => $warehouse['city'],
      'address' => $warehouse['address'],
      'phone' => $this->faker->numerify('+963 9## ### ###'), 
    ];
  }
}