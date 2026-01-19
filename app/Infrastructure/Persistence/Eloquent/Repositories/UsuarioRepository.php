<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Entities\Usuario;
use App\Domain\Enums\StatusUsuario;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\UsuarioModel;

final class UsuarioRepository implements UsuarioRepositoryInterface
{

    private function toEntity(UsuarioModel $usuario): Usuario
    {
        return new Usuario(
            $usuario->id,
            $usuario->nome,
            $usuario->email,
            $usuario->password,
            $usuario->status
        );
    }

    public function adicionar(Usuario $usuario): Usuario
    {
        $usuarioModel = UsuarioModel::create([
            'nome'        => $usuario->nome(),
            'email'       => $usuario->email(),
            'password'    => $usuario->password(),
            'status'      => $usuario->status(),
        ]);

        return $this->toEntity($usuarioModel);
    }

    public function buscarPorId(int $id): ?Usuario
    {
        $model = UsuarioModel::query()->find($id);
        if (!$model) return null;

        $usuario = new Usuario(
            $model->id,
            $model->nome,
            $model->email,
            $model->password,
            $model->status,
        );

        return $usuario;
    }

    public function buscarPorEmail(string $email): ?Usuario
    {
        $model = UsuarioModel::where('email', mb_strtolower(trim($email)))->first();
        if (!$model) return null;

        $usuario = new Usuario(
            $model->id,
            $model->nome,
            $model->email,
            $model->password,
            $model->status
        );

        return $usuario;
    }

    public function atualizar(int $id, Usuario $usuario): ?Usuario
    {
        $model = UsuarioModel::query()->find($id);
        if (!$model) return null;

        $model->nome = $usuario->nome();
        $model->email = $usuario->email();
        $model->status = $usuario->status();
        if ($usuario->password() !== '') {
            $model->password = $usuario->password();
        }
        $model->save();

        return new Usuario(
            id: $model->id,
            nome: $model->nome,
            email: $model->email,
            password: $model->password,
            status: $model->status
        );
    }

    public function excluir(int $id): bool
    {
        return (bool) UsuarioModel::query()
            ->where('id', $id)
            ->update(['status' => Usuario::STATUS_INATIVO]);
    }

    public function definirPapeis(int $usuarioId, array $papeisIds): void
    {
        $model = UsuarioModel::query()->findOrFail($usuarioId);
        $model->papeis()->sync($papeisIds);
    }
}
