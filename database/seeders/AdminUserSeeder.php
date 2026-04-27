<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
  public function run(): void
  {
    $role = Role::where('name', 'super_admin')->first();

    $admin = User::firstOrCreate(
      ['email' => 'admin@gmail.com'],
      [
        'name' => 'Admin',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
      ]
    );

    $admin->assignRole($role);

  }
}
