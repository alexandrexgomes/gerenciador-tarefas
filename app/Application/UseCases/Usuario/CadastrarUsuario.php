<?php

namespace App\Application\UseCases\Usuario;

use App\Application\DTOs\Usuario\CadastrarUsuarioDTO;
use App\Domain\Entities\Usuario;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Domain\Services\Hash\ServiceHashInterface;
use App\Domain\Services\AutorizacaoInterface;
use App\Domain\Enums\Permissao;
use App\Domain\Services\TransactionManagerInterface;
use App\Domain\Exceptions\NaoAutorizadoException;
use App\Domain\Exceptions\ConflitoException;
use App\Application\DTOs\Usuario\UsuarioResponseDTO;
use App\Application\Queries\UsuarioQueryRepositoryInterface;

final class CadastrarUsuario
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarios,
        private ServiceHashInterface $hash,
        private AutorizacaoInterface $autorizacao,
        private TransactionManagerInterface $transacaoManager,
        private UsuarioQueryRepositoryInterface $query
    ) {}

    public function executar(CadastrarUsuarioDTO $dto): UsuarioResponseDTO
    {
        if (!$this->autorizacao->temPermissao(Permissao::CRIAR_USUARIO)) {
            throw new NaoAutorizadoException('Você não tem permissão para cadastrar usuários.');
        }

        if ($this->usuarios->buscarPorEmail($dto->email)) {
            throw new ConflitoException('E-mail já cadastrado.');
        }

        return $this->transacaoManager->executar(function () use ($dto) {
            $hash = $this->hash->gerarHash($dto->password);

            $usuario = Usuario::novo(
                nome: $dto->nome,
                email: $dto->email,
                password: $hash
            );

            $usuario = $this->usuarios->adicionar($usuario);
            $this->usuarios->definirPapeis((int) $usuario->id(), $dto->papeis_ids);
            $papeis = $this->query->carregarPapeisUsuario((int) $usuario->id());
            return UsuarioResponseDTO::fromEntity($usuario, $papeis);
        });
    }
}
