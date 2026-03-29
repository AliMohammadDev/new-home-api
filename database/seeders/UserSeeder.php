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
      ['name' => 'سارة الخطيب', 'email' => 'sara@example.com', 'role' => 'product_data_entry'],
      ['name' => 'عمر الفاروق', 'email' => 'omar@example.com', 'role' => 'finance_manager'],
      ['name' => 'ليلى العامري', 'email' => 'layla@example.com', 'role' => 'main_warehouse_manager'],
      ['name' => 'محمود درويش', 'email' => 'mahmoud@example.com', 'role' => 'sub_warehouse_manager'],
      ['name' => 'محمد الحسن', 'email' => 'mohammad@example.com', 'role' => 'sub_warehouse_manager'],

      ['name' => 'نور الهدى', 'email' => 'nour@example.com', 'role' => 'sales_point_manager'],
      ['name' => 'ياسين حمزة', 'email' => 'yassin@example.com', 'role' => 'sales_point_manager'],
      ['name' => 'مريم النجار', 'email' => 'mariam@example.com', 'role' => 'sales_point_cashier'],
      ['name' => 'خالد العلي', 'email' => 'khaled@example.com', 'role' => 'sales_point_cashier'],
      ['name' => 'فاطمة الزهراء', 'email' => 'fatima@example.com', 'role' => 'sales_point_cashier'],

      ['name' => 'يوسف الحسن', 'email' => 'yousef@example.com', 'role' => 'customer'],
      ['name' => 'ريم عبدالله', 'email' => 'reem@example.com', 'role' => 'customer'],
      ['name' => 'طارق محمود', 'email' => 'tareq@example.com', 'role' => 'customer'],
      ['name' => 'دينا علي', 'email' => 'dina@example.com', 'role' => 'customer'],
      ['name' => 'حسن قاسم', 'email' => 'hasan@example.com', 'role' => 'customer'],

      ['name' => 'رنا يوسف', 'email' => 'rana@example.com', 'role' => 'customer'],
      ['name' => 'سامر خليل', 'email' => 'samer@example.com', 'role' => 'customer'],
      ['name' => 'ندى حسن', 'email' => 'nada@example.com', 'role' => 'customer'],
      ['name' => 'رامي عادل', 'email' => 'rami@example.com', 'role' => 'customer'],
      ['name' => 'جنى خالد', 'email' => 'jana@example.com', 'role' => 'customer'],

      ['name' => 'شركة النسر للتوصيل', 'email' => 'eagle@example.com', 'role' => 'delivery_company'],
      ['name' => 'توصيل البرق', 'email' => 'lightning@example.com', 'role' => 'delivery_company'],
      ['name' => 'مندوبنا السريع', 'email' => 'fast_agent@example.com', 'role' => 'delivery_company'],
      ['name' => 'خدمات اللوجستيك العربية', 'email' => 'arab_logistics@example.com', 'role' => 'delivery_company'],
      ['name' => 'شركة الثقة للنقل', 'email' => 'trust_transport@example.com', 'role' => 'delivery_company'],
    ];

    foreach ($users as $userData) {
      $user = User::updateOrCreate(
        ['email' => $userData['email']],
        [
          'name' => $userData['name'],
          'password' => Hash::make('password'),
          'email_verified_at' => now(),
        ]
      );

      $user->syncRoles([$userData['role']]);
    }
  }
}