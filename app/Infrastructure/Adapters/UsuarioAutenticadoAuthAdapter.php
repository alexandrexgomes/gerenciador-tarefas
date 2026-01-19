<?php

namespace App\Infrastructure\Adapters;

use App\Domain\Exceptions\NaoAutenticadoException;
use App\Domain\Services\UsuarioAutenticadoInterface;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

final class UsuarioAutenticadoAuthAdapter implements UsuarioAutenticadoInterface
{
    public function __construct(private AuthFactory $auth) {}

    public function id(): int
    {
        $id = $this->auth->guard('api')->id();

        if (!$id) {
            throw new NaoAutenticadoException();
        }

        return (int) $id;
    }
}
