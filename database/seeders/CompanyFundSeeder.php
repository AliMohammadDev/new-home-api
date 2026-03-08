<?php

namespace Database\Seeders;

use App\Models\CompanyFund;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyFundSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    CompanyFund::updateOrCreate(
      ['name' => 'الصندوق الرئيسي'],
      [
        'balance' => 5000,
      ]
    );
  }
}
