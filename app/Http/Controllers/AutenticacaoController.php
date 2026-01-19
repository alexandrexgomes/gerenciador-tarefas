<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutenticarRequest;
use App\Application\DTOs\AutenticarDTO;
use App\Application\UseCases\Autenticacao\AutenticacaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Presentation\Presenters\AutenticacaoPresenter;

class AutenticacaoController extends Controller
{
    public function login(AutenticarRequest $request, AutenticacaoService $auth): JsonResponse
    {
        try {
            $dto = new AutenticarDTO(...$request->dados());
            $tokenDTO = $auth->autenticar($dto);

            return response()->json([
                'token' => $tokenDTO->token,
                'tipo'  => 'bearer',
                'expira_em_segundos' => $tokenDTO->expiraEmSegundos,
            ], 200);
        } catch (\DomainException $e) {
            return response()->json(['mensagem' => $e->getMessage()], 401);
        }
    }

    public function perfil(AutenticacaoService $auth): JsonResponse
    {
        $usuario = $auth->usuarioAutenticado();
        return response()->json(AutenticacaoPresenter::perfil($usuario), 200);
    }

    public function sair(): JsonResponse
    {
        Auth::guard('api')->logout();
        return response()->json(['mensagem' => 'Sessão encerrada.'], 200);
    }

    public function refresh(): JsonResponse
    {
        $newToken = Auth::guard('api')->refresh();
        $ttlMin   = (int) config('jwt.ttl', 60);

        return response()->json([
            'token'              => $newToken,
            'tipo'               => 'bearer',
            'expira_em_segundos' => $ttlMin * 60,
        ], 200);
    }
}
