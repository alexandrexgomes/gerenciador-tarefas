<?php

namespace App\Application\UseCases\Usuario;

use App\Domain\Exceptions\ConflitoException;
use App\Application\DTOs\Usuario\AtualizarUsuarioDTO;
use App\Domain\Entities\Usuario;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Domain\Services\AutorizacaoInterface;
use App\Domain\Enums\Permissao;
use App\Domain\Exceptions\NaoAutorizadoException;
use App\Domain\Services\Hash\ServiceHashInterface;
use App\Domain\Exceptions\RecursoNaoEncontradoException;
use App\Domain\Exceptions\RegraNegocioException;

final class AtualizarUsuario
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarios,
        private AutorizacaoInterface $autorizacao,
        private ServiceHashInterface $hasher
    ) {}

    public function executar(AtualizarUsuarioDTO $dto): Usuario
    {
        if (!$this->autorizacao->temPermissao(Permissao::ATUALIZAR_USUARIO)) {
            throw new NaoAutorizadoException('Você não tem permissão para atualizar usuários.');
        }

        $atual = $this->usuarios->buscarPorId($dto->id);
        if (!$atual) {
            throw new RecursoNaoEncontradoException('Usuário não encontrado.');
        }

        $atualEmail = $this->usuarios->buscarPorEmail($dto->email);
        if ($atualEmail && $atualEmail->id() !== $dto->id) {
            throw new ConflitoException('Já existe um usuário com este e-mail.');
        }

        $senhaHash = '';
        if ($dto->password !== null && trim($dto->password) !== '') {
            $senhaHash = $this->hasher->gerarHash($dto->password);
        }

        $usuario = new Usuario(
            id: $dto->id,
            nome: $dto->nome,
            email: $dto->email,
            password: $senhaHash,
            status: $dto->status,
        );

        $response = $this->usuarios->atualizar($dto->id, $usuario);
        $this->usuarios->definirPapeis($dto->id, $dto->papeis_ids);

        if (!$response) {
            throw new RegraNegocioException('Falha ao atualizar o usuario.');
        }

        return $response;
    }
}
