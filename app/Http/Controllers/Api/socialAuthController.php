<?php

namespace App\Http\Controllers\Api;

use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use App\Models\User;

class socialAuthController extends Controller
{
  public function redirectToProvider()
  {
    return Socialite::driver('google')->stateless()->redirect();
  }


  public function handleCallback()
  {
    $google_user = Socialite::driver('google')
      ->stateless()
      ->user();

    $user = User::where('google_id', $google_user->id)
      ->orWhere('email', $google_user->email)
      ->first();

    if ($user) {
      $user->update([
        'google_id' => $user->google_id ?? $google_user->id,
        'google_token' => $google_user->token,
        'name' => $user->name ?? $google_user->name,
      ]);
    } else {
      $user = User::create([
        'name' => $google_user->name,
        'email' => $google_user->email,
        'google_id' => $google_user->id,
        'google_token' => $google_user->token,
        'password' => null,
      ]);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    // return redirect("http://localhost:5173/google-callback?token=$token");
    return redirect("https://almanzel-alhadith.com/google-callback?token=$token");
  }

}