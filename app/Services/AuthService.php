<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Checkout;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthService
{
  public function registerUser(array $data)
  {
    $user = User::create([
      'name' => $data['name'],
      'email' => $data['email'],
      'password' => Hash::make($data['password']),
    ]);
    $token = $user->createToken('auth_token')->plainTextToken;
    return $token;
  }

  public function loginUser(array $data)
  {
    $user = User::where('email', $data['email'])->first();
    if (!$user || !Hash::check($data['password'], $user->password)) {
      return null;
    }
    $user->tokens()->delete();
    $token = $user->createToken('auth_token')->plainTextToken;
    return $token;
  }

  // public function updateProfile(User $user, array $data): User
  // {
  //   if (isset($data['password'])) {
  //     $data['password'] = Hash::make($data['password']);
  //   }
  //   $user->update($data);
  //   return $user;
  // }

  public function updateProfile(User $user, array $data): User
  {
    $user->update(collect($data)->only(['name', 'email', 'password'])->filter()->toArray());

    $activeCart = $user->activeCart;

    Checkout::updateOrCreate(
      ['user_id' => $user->id],
      [
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'country' => $data['country'],
        'city' => $data['city'],
        'shipping_city_id' => $data['shipping_city_id'] ?? null,
        'street' => $data['street'] ?? '',
        'floor' => $data['floor'] ?? '',
        'postal_code' => $data['postal_code'] ?? null,
        'additional_information' => $data['additional_information'] ?? null,
        'cart_id' => $activeCart ? $activeCart->id : null,
      ]
    );
    return $user;
  }

}