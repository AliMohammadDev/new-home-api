<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $idempotency = $request->headers->get("X-Idempotency-key");

    if (!$idempotency) {
      return $next($request);
    }

    if (Cache::has("idempotency_{$idempotency}")) {
      $cacheResponse = Cache::get("idempotency_{$idempotency}");
      return response()->json(
        $cacheResponse['original'] ? $cacheResponse['original'] : $cacheResponse,
        Response::HTTP_OK,
        ["X-Cache" => "HIT IDEMPOTENT"]
      );
    }
    $response = $next($request);

    if ($response->isSuccessful()) {
      Cache::put("idempotency_{$idempotency}", $response, now()->addHours(10));
    }

    return $response;
  }
}
