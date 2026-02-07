<?php

namespace Database\Seeders;

use App\Models\ShippingCity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingCitySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $cities = [
      [
        'city_name' => ['en' => 'Damascus', 'ar' => 'دمشق'],
        'estimated_delivery' => '24 - 48 Hours',
        'shipping_fee' => 0,
        'is_free_shipping' => true,
      ],
      [
        'city_name' => ['en' => 'Rif Damascus', 'ar' => 'ريف دمشق'],
        'estimated_delivery' => '48 - 72 Hours',
        'shipping_fee' => 10,
        'is_free_shipping' => false,
      ],
      [
        'city_name' => ['en' => 'Aleppo', 'ar' => 'حلب'],
        'estimated_delivery' => '3 - 4 Days',
        'shipping_fee' => 20,
        'is_free_shipping' => false,
      ],
      [
        'city_name' => ['en' => 'Homs', 'ar' => 'حمص'],
        'estimated_delivery' => '2 - 3 Days',
        'shipping_fee' => 15,
        'is_free_shipping' => false,
      ],
      [
        'city_name' => ['en' => 'Hama', 'ar' => 'حماة'],
        'estimated_delivery' => '2 - 3 Days',
        'shipping_fee' => 15,
        'is_free_shipping' => false,
      ],
      [
        'city_name' => ['en' => 'Lattakia', 'ar' => 'اللاذقية'],
        'estimated_delivery' => '3 - 4 Days',
        'shipping_fee' => 18,
        'is_free_shipping' => false,
      ],
      [
        'city_name' => ['en' => 'Tartous', 'ar' => 'طرطوس'],
        'estimated_delivery' => '3 - 4 Days',
        'shipping_fee' => 18,
        'is_free_shipping' => false,
      ],
      [
        'city_name' => ['en' => 'Daraa', 'ar' => 'درعا'],
        'estimated_delivery' => '2 - 3 Days',
        'shipping_fee' => 12,
        'is_free_shipping' => false,
      ],
      [
        'city_name' => ['en' => 'Sweida', 'ar' => 'السويداء'],
        'estimated_delivery' => '2 - 3 Days',
        'shipping_fee' => 12,
        'is_free_shipping' => false,
      ],
      [
        'city_name' => ['en' => 'Quneitra', 'ar' => 'القنيطرة'],
        'estimated_delivery' => '2 - 3 Days',
        'shipping_fee' => 12,
        'is_free_shipping' => false,
      ],
      [
        'city_name' => ['en' => 'Deir ez-Zor', 'ar' => 'دير الزور'],
        'estimated_delivery' => '5 - 7 Days',
        'shipping_fee' => 25,
        'is_free_shipping' => false,
      ],
      [
        'city_name' => ['en' => 'Hasakah', 'ar' => 'الحسكة'],
        'estimated_delivery' => '5 - 7 Days',
        'shipping_fee' => 25,
        'is_free_shipping' => false,
      ],
      [
        'city_name' => ['en' => 'Raqqa', 'ar' => 'الرقة'],
        'estimated_delivery' => '5 - 7 Days',
        'shipping_fee' => 25,
        'is_free_shipping' => false,
      ],
      [
        'city_name' => ['en' => 'Idlib', 'ar' => 'إدلب'],
        'estimated_delivery' => '3 - 5 Days',
        'shipping_fee' => 20,
        'is_free_shipping' => false,
      ],
    ];

    foreach ($cities as $cityData) {
      ShippingCity::updateOrCreate(
        ['city_name->en' => $cityData['city_name']['en']],
        [
          'city_name' => $cityData['city_name'],
          'estimated_delivery' => $cityData['estimated_delivery'],
          'shipping_fee' => $cityData['shipping_fee'],
          'is_free_shipping' => $cityData['is_free_shipping'],
          'is_active' => true,
        ]
      );
    }
  }
}