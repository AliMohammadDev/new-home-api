<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{

  public function store(Request $request)
  {
    $data = $request->validate([
      'endpoint' => 'required',
      'publicKey' => 'required',
      'authToken' => 'required',
    ]);

    PushSubscription::updateOrCreate(
      ['user_id' => Auth::id()],
      [
        'endpoint' => $data['endpoint'],
        'public_key' => $data['publicKey'],
        'auth_token' => $data['authToken'],
      ]
    );

    return response()->json(['success' => true]);
  }
}