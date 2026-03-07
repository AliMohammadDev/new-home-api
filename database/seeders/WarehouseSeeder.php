<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
  public function run(): void
  {
    $warehouses = [
      ['name' => 'مستودع الفيحاء المركزي', 'city' => 'دمشق', 'address' => 'المنطقة الصناعية - ركن الدين'],
      ['name' => 'مستودع الشهباء الرئيسي', 'city' => 'حلب', 'address' => 'الليرمون - حي الصناعة'],
      ['name' => 'مستودع الخليج اللوجستي', 'city' => 'دبي', 'address' => 'جبل علي - المنطقة الحرة'],
      ['name' => 'مستودع النيل للتوريدات', 'city' => 'القاهرة', 'address' => 'مدينة السادس من أكتوبر'],
      ['name' => 'مستودع المتوسط الشامل', 'city' => 'اللاذقية', 'address' => 'قرب المرفأ - المنطقة الحرة'],
      ['name' => 'مستودع الرياض المتطور', 'city' => 'الرياض', 'address' => 'حي السلي - طريق الخرج'],
    ];

    foreach ($warehouses as $warehouse) {
      Warehouse::create([
        'name' => $warehouse['name'],
        'city' => $warehouse['city'],
        'address' => $warehouse['address'],
        'phone' => fake()->numerify('+963 9## ### ###'),
      ]);
    }
  }
}