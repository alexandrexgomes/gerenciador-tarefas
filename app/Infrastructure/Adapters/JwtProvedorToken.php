<?php

namespace App\Infrastructure\Adapters;

use App\Application\DTOs\AutenticadoDTO;
use App\Application\DTOs\TokenDTO;
use App\Domain\Entities\Usuario;
use App\Domain\Services\ProvedorTokenInterface;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Infrastructure\Persistence\Eloquent\Models\UsuarioModel;

class JwtProvedorToken implements ProvedorTokenInterface
{
    public function gerarToken(Usuario $usuario): TokenDTO
    {
        $model = UsuarioModel::findOrFail($usuario->id());

        $token = JWTAuth::fromUser($model);
        $ttlMin = (int) config('jwt.ttl', 60);
        return new TokenDTO($token, $ttlMin * 60);
    }

    public function usuarioAutenticado(): ?AutenticadoDTO
    {
        $usuario = JWTAuth::parseToken()->authenticate();

        if (!$usuario instanceof UsuarioModel) {
            return null;
        }

        $permissoes = $usuario->papeis
            ->flatMap(fn($papel) => $papel->permissoes->pluck('nome'))
            ->unique()
            ->values()
            ->all();

        return new AutenticadoDTO(
            id: $usuario->id,
            nome: $usuario->nome,
            email: $usuario->email,
            status: (int) $usuario->status,
            permissoes: $permissoes
        );
    }
}
