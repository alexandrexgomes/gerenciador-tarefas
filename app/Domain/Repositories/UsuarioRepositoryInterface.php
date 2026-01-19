<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Usuario;

interface UsuarioRepositoryInterface
{
    public function adicionar(Usuario $usuario): Usuario;
    public function buscarPorId(int $id): ?Usuario;
    public function buscarPorEmail(string $email): ?Usuario;

    public function excluir(int $id): bool;
    public function atualizar(int $id, Usuario $usuario): ?Usuario;

    /** @param int[] $papeisIds */
    public function definirPapeis(int $usuarioId, array $papeisIds): void;
}
