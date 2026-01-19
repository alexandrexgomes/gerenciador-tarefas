<?php

namespace Tests\Concerns;

use App\Infrastructure\Persistence\Eloquent\Models\UsuarioModel;
use Tymon\JWTAuth\Facades\JWTAuth;

trait AutenticaComJwt
{
    protected function headersJwt(UsuarioModel $user): array
    {
        $token = JWTAuth::fromUser($user);

        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];
    }
}
