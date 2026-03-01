<?php

namespace Database\Seeders;

use App\Models\SalesPoint;
use App\Models\SalesPointCashier;
use App\Models\User;
use Illuminate\Database\Seeder;

class CashierSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $salesPoints = SalesPoint::all();
    $users = User::whereDoesntHave('salesPoints')->limit(5)->get();

    foreach ($users as $index => $user) {
      SalesPointCashier::create([
        'sales_point_id' => $salesPoints->random()->id,
        'user_id' => $user->id,
        'shift_type' => $index % 2 == 0 ? 'صباحي' : 'مسائي',
        'daily_limit' => 500000,
      ]);
    }
  }
}