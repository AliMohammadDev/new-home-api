<?php

namespace Database\Seeders;

use App\Models\SalesPoint;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class SalesPointSeeder extends Seeder
{
  public function run(): void
  {
    $warehouses = Warehouse::factory()->count(5)->create();

    $salesPointsData = [
      ['name' => 'فرع المزة الرئيسي', 'location' => 'دمشق - أوتوستراد المزة', 'phone' => '0112233445'],
      ['name' => 'نقطة بيع الشعلان', 'location' => 'دمشق - شارع الشعلان', 'phone' => '0115566778'],
      ['name' => 'مركز جرمانا التجاري', 'location' => 'ريف دمشق - ساحة جرمانا', 'phone' => '0118899001'],
      ['name' => 'فرع حلب - الشهباء', 'location' => 'حلب - حي الشهباء', 'phone' => '0214455667'],
      ['name' => 'نقطة اللاذقية الكورنيش', 'location' => 'اللاذقية - الكورنيش الغربي', 'phone' => '0412211334'],
    ];

    $users = User::limit(10)->get();

    foreach ($salesPointsData as $data) {
      $sp = SalesPoint::create([
        'warehouse_id' => $warehouses->random()->id,
        'name' => $data['name'],
        'location' => $data['location'],
        'phone' => $data['phone'],
        'is_active' => true,
      ]);

      if ($users->count() > 0) {
        $randomManagers = $users->random(rand(1, min(2, $users->count())));

        foreach ($randomManagers as $user) {
          $sp->managers()->attach($user->id, [
            'phone' => '09' . rand(30, 99) . rand(100, 999) . rand(100, 999),
            'created_at' => now(),
            'updated_at' => now(),
          ]);
        }
      }
    }
  }
}