<?php

namespace App\Application\UseCases\Usuario;

use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Domain\Services\AutorizacaoInterface;
use App\Domain\Enums\Permissao;
use App\Domain\Exceptions\NaoAutorizadoException;

final class ExcluirUsuario
{
    public function __construct(
        private UsuarioRepositoryInterface $repo,
        private AutorizacaoInterface $autorizacao,
    ) {}

    public function executar(int $id): bool
    {
        if (!$this->autorizacao->temPermissao(Permissao::EXCLUIR_USUARIO)) {
            throw new NaoAutorizadoException('Você não tem permissão para excluir usuários.');
        }

        return $this->repo->excluir($id);
    }
}
