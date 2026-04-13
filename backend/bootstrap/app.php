<?php

use App\Exceptions\BusinessException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request, \Throwable $e): bool {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (BusinessException $e, $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatus());
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            $status = 500;
            $message = 'Erro interno do servidor';
            $errors = null;

            if ($e instanceof ValidationException) {
                $status = 422;
                $message = 'Erro de validação';
                $errors = $e->errors();
            } elseif ($e instanceof AuthenticationException) {
                $status = 401;
                $message = 'Não autenticado';
            } elseif ($e instanceof AuthorizationException) {
                $status = 403;
                $message = 'Acesso negado';
            } elseif ($e instanceof NotFoundHttpException) {
                $status = 404;
                $message = 'Recurso não encontrado';
            } elseif ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $message = $e->getMessage() ?: 'Requisição inválida';
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], $status);
        });
    })->create();
