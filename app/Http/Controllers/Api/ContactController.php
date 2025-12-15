<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactUsMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
  public function send(Request $request)
  {
    $data = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email',
      'message' => 'required|string',
    ]);

    try {
      Mail::to(config('mail.from.address'))
        ->send(new ContactUsMail($data));

      return response()->json([
        'message' => 'Message sent successfully'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Failed to send message',
        'error' => $e->getMessage()
      ], 500);
    }
  }

}