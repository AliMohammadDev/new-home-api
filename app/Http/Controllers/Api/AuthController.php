<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\CreateUserRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\Controller;
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
    $result = $this->authService->loginUser($request->validated());
    return response()->json(["token" => $result]);
  }

  public function me()
  {
    return response()->json([
      'user' => Auth::User(),
    ], 200);
  }
}
