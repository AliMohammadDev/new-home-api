<?php

namespace Database\Seeders;

use App\Models\CompanyFund;
use App\Models\CompanyTreasure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
  }
}