<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
  /**
   * Send reset password link
   */
  public function forgotPassword(ForgetPasswordRequest $request)
  {
    $email = $request->email;
    $user = User::where('email', $email)->first();
    if (!$user) {
      return response()->json([
        'message' => 'User not found'
      ], 404);
    }

    // delete old tokens
    DB::table('password_reset_tokens')->where('email', $email)->delete();

    $token = Str::random(64);

    DB::table('password_reset_tokens')->insert([
      'email' => $email,
      'token' => Hash::make($token),
      'created_at' => Carbon::now()
    ]);

    // send mail
    Mail::send('mails.forgetPassword', ['token' => $token, 'email' => $email], function ($message) use ($email) {
      $message->to($email);
      $message->subject('Reset Password');
    });

    return response()->json([
      'message' => 'Reset password link sent to your email'
    ], 200);
  }

  /**
   * Reset password
   */
  public function resetPassword(ResetPasswordRequest $request)
  {
    $record = DB::table('password_reset_tokens')
      ->where('email', $request->email)
      ->first();

    if (!$record) {
      return response()->json([
        'message' => 'Invalid reset request'
      ], 400);
    }

    // check token
    if (!Hash::check($request->token, $record->token)) {
      return response()->json([
        'message' => 'Invalid token'
      ], 400);
    }

    // check token expiration (60 minutes)
    if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
      return response()->json([
        'message' => 'Token expired'
      ], 400);
    }

    User::where('email', $request->email)->update([
      'password' => Hash::make($request->password)
    ]);

    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return response()->json([
      'message' => 'Password reset successfully'
    ], 200);
  }
}