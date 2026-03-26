<?php

namespace Database\Seeders;

use App\Models\CompanyTreasure;
use Illuminate\Database\Seeder;

class CompanyTreasureSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    CompanyTreasure::updateOrCreate(
      ['name' => 'الصندوق الرئيسي'],
      [
        'money' => 5000,
      ]
    );

    CompanyTreasure::updateOrCreate(
      ['name' => 'صندوق مبيعات المتجر الالكتروني'],
      [
        'money' => 0,
      ]
    );
  }
}