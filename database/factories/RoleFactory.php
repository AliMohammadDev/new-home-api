<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

class RoleFactory extends Factory
{
  protected $model = Role::class;

  public static array $roles = [
    'admin' => 'Admin',
    'accountant' => 'Accountant',
    'customer' => 'Customer',
    'warehouse_manager' => 'Warehouse Manager',
    'cashier' => 'Cashier',
  ];

  public function definition(): array
  {
    return [
      'name' => $this->faker->unique()->randomElement(array_keys(self::$roles)),
      'guard_name' => 'web',
    ];
  }
}