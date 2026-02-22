<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\IdempotencyMiddleware;
use App\Http\Middleware\SetLocalMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    channels: __DIR__ . '/../routes/channels.php',
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
      'idempotency' => IdempotencyMiddleware::class,
      'setLocale' => SetLocalMiddleware::class,
      'admin' => AdminMiddleware::class,

      'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
      'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
      'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);

    $middleware->validateCsrfTokens(except: [
      'api/save-fcm-token',
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    //
  })->create();
