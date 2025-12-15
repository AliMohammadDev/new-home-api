<?php

namespace App\Services;

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

  public function updateProfile(User $user, array $data): User
  {
    if (isset($data['password'])) {
      $data['password'] = Hash::make($data['password']);
    }
    $user->update($data);
    return $user;
  }
}
