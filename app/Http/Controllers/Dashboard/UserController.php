<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Auth\CreateUserRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Services\Dashboard\UserServices;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
  public function __construct(
    private UserServices $userService
  ) {
  }

  public function index()
  {
    $users = $this->userService->findAll();
    return UserResource::collection($users);
  }

  public function store(CreateUserRequest $request)
  {
    $user = $this->userService->create($request->validated());
    return new UserResource($user);
  }

  public function show(User $user)
  {
    return new UserResource($user);
  }

  public function update(User $user, UpdateUserRequest $request)
  {
    $newUser = $this->userService->update($user, $request->validated());
    return new UserResource($newUser);
  }
  public function destroy(User $user)
  {
    $user = $this->userService->delete($user);
    return response()->json(['message' => 'User deleted successfully']);
  }
}