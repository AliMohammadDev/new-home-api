<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class FcmService
{

  public static function sendNotification($token, $title, $body, $data = [])
  {
    $file = storage_path('app/firebase-auth.json');

    if (!file_exists($file)) {
      return ['error' => 'File not found at ' . $file];
    }

    $credentials = new ServiceAccountCredentials(
      'https://www.googleapis.com/auth/cloud-platform',
      $file
    );

    $tokenArray = $credentials->fetchAuthToken(HttpHandlerFactory::build());
    $accessToken = $tokenArray['access_token'];

    $projectId = json_decode(file_get_contents($file))->project_id;

    $payload = [
      'token' => $token,
      'notification' => [
        'title' => $title,
        'body' => $body,
        'image' => asset('logo.png'),
      ],
      'webpush' => [
        'notification' => [
          'icon' => asset('logo.png'),
          'badge' => asset('logo.png'),
          'vibrate' => [200, 100, 200],
        ],
      ],
    ];

    if (!empty($data)) {
      $payload['data'] = array_map('strval', $data);
    }

    $response = Http::withToken($accessToken)->post(
      "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
      [
        'message' => $payload
      ]
    );

    return $response->json();
  }
}
