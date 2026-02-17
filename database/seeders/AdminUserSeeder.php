<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
  public function run(): void
  {
    $role = Role::firstOrCreate(['name' => 'super_admin']);

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
