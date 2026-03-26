<?php

namespace Database\Seeders;

use App\Models\DeliveryCompany;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeliveryCompanySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $users = User::role('delivery_company')->get();



    $companies = [
      ['name' => 'أرامكس (Aramex)', 'phone' => '0599000111', 'address' => 'المنطقة الصناعية'],
      ['name' => 'دي اتش ال (DHL Express)', 'phone' => '0599000222', 'address' => 'شارع المطار'],
      ['name' => 'شركة سمسا (SMSA)', 'phone' => '0599000333', 'address' => 'حي الروضة'],
      ['name' => 'مرسول (Mrsool)', 'phone' => '0599000444', 'address' => 'فرع التوصيل السريع'],
      ['name' => 'ناقل اكسبريس (Naqel)', 'phone' => '0599000555', 'address' => 'طريق الملك فهد'],
    ];

    foreach ($companies as $company) {
      DeliveryCompany::create([
        'user_id' => $users->random()->id,
        'name' => $company['name'],
        'phone' => $company['phone'],
        'address' => $company['address'],
        'is_active' => true,
      ]);
    }

  }
}