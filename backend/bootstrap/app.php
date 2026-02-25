<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (BusinessException $e, $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatus());
        });

        $exceptions->render(function (Throwable $e, $request) {

            if (! $request->expectsJson()) {
                return null; 
            }

            $status = 500;
            $message = 'Erro interno do servidor';
            $errors = null;

            if ($e instanceof ValidationException) {
                $status = 422;
                $message = 'Erro de validação';
                $errors = $e->errors();
            }

            elseif ($e instanceof HttpException){
                $status = 400;
                $message = 'Requisição inválida';
            }

            elseif ($e instanceof AuthenticationException) {
                $status = 401;
                $message = 'Não autenticado';
            }

            elseif($e instanceof AuthorizationException) {
                $status = 403;
                $message = 'Acesso negado';
            }

            elseif ($e instanceof NotFoundHttpException) {
                $status = 404;
                $message = 'Recurso não encontrado';
            }

            elseif ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $message = $e->getMessage() ?: 'Erro HTTP';
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors'  => $errors,
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], $status);
        });
    })->create();

