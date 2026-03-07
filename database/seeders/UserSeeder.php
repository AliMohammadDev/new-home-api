<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  public function run(): void
  {
    $users = [
      ['name' => 'أحمد المنصور', 'email' => 'ahmed@example.com'],
      ['name' => 'سارة الخطيب', 'email' => 'sara@example.com'],
      ['name' => 'عمر الفاروق', 'email' => 'omar@example.com'],
      ['name' => 'ليلى العامري', 'email' => 'layla@example.com'],
      ['name' => 'محمود درويش', 'email' => 'mahmoud@example.com'],

      ['name' => 'نور الهدى', 'email' => 'nour@example.com'],
      ['name' => 'ياسين حمزة', 'email' => 'yassin@example.com'],
      ['name' => 'مريم النجار', 'email' => 'mariam@example.com'],
      ['name' => 'خالد العلي', 'email' => 'khaled@example.com'],
      ['name' => 'فاطمة الزهراء', 'email' => 'fatima@example.com'],

      ['name' => 'يوسف الحسن', 'email' => 'yousef@example.com'],
      ['name' => 'ريم عبدالله', 'email' => 'reem@example.com'],
      ['name' => 'طارق محمود', 'email' => 'tareq@example.com'],
      ['name' => 'دينا علي', 'email' => 'dina@example.com'],
      ['name' => 'حسن قاسم', 'email' => 'hasan@example.com'],

      ['name' => 'رنا يوسف', 'email' => 'rana@example.com'],
      ['name' => 'سامر خليل', 'email' => 'samer@example.com'],
      ['name' => 'ندى حسن', 'email' => 'nada@example.com'],
      ['name' => 'رامي عادل', 'email' => 'rami@example.com'],
      ['name' => 'جنى خالد', 'email' => 'jana@example.com'],
    ];

    foreach ($users as $index => $userData) {

      $user = User::create([
        'name' => $userData['name'],
        'email' => $userData['email'],
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
      ]);

      if ($index < 5) {
        $user->assignRole('sales_point_manger');
      } elseif ($index < 10) {
        $user->assignRole('sales_point_cashier');
      } else {
        $user->assignRole('customer');
      }
    }
  }
}
