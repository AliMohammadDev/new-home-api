<?php

namespace App\Services;

use App\Models\Cart;
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
      return response()->json([
        'message' => 'Invalid email or password',
      ], 401);
    }
    $user->tokens()->delete();
    $token = $user->createToken('auth_token')->plainTextToken;
    return $token;
  }
}
