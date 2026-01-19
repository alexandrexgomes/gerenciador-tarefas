<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Entities\Tarefa;
use App\Domain\Repositories\TarefaRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\TarefaModel;

final class TarefaRepository implements TarefaRepositoryInterface
{
    public function criar(Tarefa $tarefa): Tarefa
    {
        $model = TarefaModel::create([
            'title' => $tarefa->title(),
            'description' => $tarefa->description(),
            'completed' => $tarefa->completed(),
        ]);

        return $this->mapToEntity($model);
    }

    public function buscarPorId(int $id): ?Tarefa
    {
        $model = TarefaModel::query()->find($id);

        return $model ? $this->mapToEntity($model) : null;
    }

    public function atualizar(Tarefa $tarefa): Tarefa
    {
        $id = $tarefa->id();
        if (!$id) {
            throw new \InvalidArgumentException('Nao e possivel atualizar uma tarefa sem id.');
        }

        $model = TarefaModel::query()->findOrFail($id);

        $model->update([
            'title' => $tarefa->title(),
            'description' => $tarefa->description(),
            'completed' => $tarefa->completed(),
        ]);

        return $this->mapToEntity($model->fresh());
    }

    public function excluir(int $id): bool
    {
        return TarefaModel::query()->whereKey($id)->delete();
    }

    private function mapToEntity(TarefaModel $model): Tarefa
    {
        return new Tarefa(
            id: $model->id,
            title: $model->title,
            description: $model->description,
            completed: $model->completed,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
        );
    }
}
