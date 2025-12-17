<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\CreateUserRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

  public function __construct(
    private AuthService $authService
  ) {
  }
  public function register(CreateUserRequest $request)
  {
    $result = $this->authService->registerUser($request->validated());
    return response()->json(['token' => $result]);
  }

  public function login(LoginRequest $request)
  {
    $token = $this->authService->loginUser($request->validated());
    if (!$token) {
      return response()->json([
        'message' => 'Invalid email or password',
      ], 401);
    }
    return response()->json(["token" => $token]);
  }

  public function me()
  {
    $user = Auth::user()->load('activeCart');
    return response()->json([
      'user' => $user,
    ], 200);
  }

  public function updateProfile(UpdateProfileRequest $request)
  {
    $user = $this->authService->updateProfile(
      Auth::user(),
      $request->validated()
    );
    return response()->json([
      'message' => 'Profile updated successfully',
      'user' => $user,
    ]);
  }
}