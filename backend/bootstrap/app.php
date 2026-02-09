<?php

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                // Tentukan status code
                $statusCode = 500;
                if ($e instanceof NotFoundHttpException) {
                    $statusCode = 404;
                } elseif (method_exists($e, 'getStatusCode')) {
                    $statusCode = $e->getStatusCode();
                }

                // Return JSON dengan pesan error asli
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(), // Pesan error utama
                    'type' => get_class($e), // Jenis error (misal: BadMethodCallException)
                    'file' => $e->getFile(), // File penyebab error (Hapus baris ini jika sudah fix demi keamanan)
                    'line' => $e->getLine(), // Baris error (Hapus baris ini jika sudah fix demi keamanan)
                ], $statusCode);
            }
        });
    })->create();
