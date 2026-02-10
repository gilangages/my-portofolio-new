<?php

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
            // 1. Biarkan Laravel menangani Validation Error secara otomatis (Return 422)
            if ($e instanceof ValidationException) {
                return null;
            }

            if ($request->is('api/*')) {
                $statusCode = 500;
                if ($e instanceof NotFoundHttpException) {
                    $statusCode = 404;
                } elseif (method_exists($e, 'getStatusCode')) {
                    $statusCode = $e->getStatusCode();
                }

                // 2. SECURITY FIX: Jangan tampilkan file & line di Production!
                $response = [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'type' => get_class($e),
                ];

                // Hanya tampilkan detail file jika mode DEBUG menyala (Local)
                if (config('app.debug')) {
                    $response['file'] = $e->getFile();
                    $response['line'] = $e->getLine();
                    $response['trace'] = $e->getTraceAsString(); // Opsional
                }

                return response()->json($response, $statusCode);
            }
        });
    })->create();
