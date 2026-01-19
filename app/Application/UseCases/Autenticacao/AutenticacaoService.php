<?php

namespace App\Application\UseCases\Autenticacao;

use App\Application\DTOs\AutenticarDTO;
use App\Application\DTOs\AutenticadoDTO;
use App\Application\DTOs\TokenDTO;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Domain\Services\ProvedorTokenInterface;
use App\Domain\Entities\Usuario;
use App\Domain\Services\Hash\ServiceHashInterface;
use App\Domain\Exceptions\CredenciaisInvalidasException;

class AutenticacaoService
{
    public function __construct(
        private UsuarioRepositoryInterface $repo,
        private ServiceHashInterface $hash,
        private ProvedorTokenInterface $tokens
    ) {}

    public function autenticar(AutenticarDTO $entrada): TokenDTO
    {
        $usuario = $this->repo->buscarPorEmail($entrada->email);

        if (!$usuario instanceof Usuario) {
            throw new CredenciaisInvalidasException('Credenciais inválidas.');
        }

        if (!$this->hash->verificar($entrada->password, $usuario->password())) {
            throw new CredenciaisInvalidasException('Credenciais inválidas.');
        }

        return $this->tokens->gerarToken($usuario);
    }
    public function usuarioAutenticado(): ?AutenticadoDTO
    {
        $usuario = $this->tokens->usuarioAutenticado();

        if (!$usuario instanceof AutenticadoDTO) {
            throw new CredenciaisInvalidasException('Usuário não autenticado.');
        }

        return $usuario;
    }

    public function buscarPorEmail(AutenticarDTO $auth): ?Usuario
    {
        return $this->repo->buscarPorEmail($auth->email);
    }
}
