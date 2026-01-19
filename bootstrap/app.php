<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use App\Domain\Exceptions\NaoAutorizadoException;
use App\Domain\Exceptions\CredenciaisInvalidasException;
use App\Domain\Exceptions\TarefaNaoEncontradaException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (NaoAutorizadoException $e, Request $request) {
            return response()->json(['message' => $e->getMessage()], 403);
        });

        $exceptions->render(function (CredenciaisInvalidasException $e, Request $request) {
            return response()->json(['message' => $e->getMessage()], 401);
        });

        $exceptions->render(function (TarefaNaoEncontradaException $e, Request $request) {
            return response()->json(['message' => $e->getMessage()], 404);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json(['message' => 'Usuário não autenticado'], 401);
        });

        $exceptions->render(function (UnauthorizedHttpException $e, Request $request) {
            return response()->json(['message' => 'Token expirado ou inválido'], 401);
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'message' => 'Dados inválidos.',
                'errors'    => $e->errors(),
            ], 422);
        });
    })->create();
