<?php

namespace App\Http\Controllers;

use App\Application\DTOs\Tarefa\CriarTarefaDTO;
use App\Application\UseCases\Tarefa\CriarTarefa;
use App\Http\Requests\CriarTarefaRequest;
use App\Presentation\Presenters\TarefaPresenter;
use App\Application\DTOs\Tarefa\AtualizarTarefaDTO;
use App\Application\UseCases\Tarefa\AtualizarTarefa;
use App\Http\Requests\AtualizarTarefaRequest;
use Illuminate\Http\JsonResponse;
use App\Application\Services\TarefaService;
use App\Application\UseCases\Tarefa\ConcluirTarefa;
use App\Application\UseCases\Tarefa\ReabrirTarefa;
use App\Application\UseCases\Tarefa\ExcluirTarefa;
use App\Http\Requests\PaginateTarefaRequest;

class TarefaController extends Controller
{

    public function index(TarefaService $service): JsonResponse
    {
        $usuarios = $service->listar();
        return response()->json(TarefaPresenter::colecao($usuarios), 200);
    }

    public function criarTarefa(CriarTarefaRequest $request, CriarTarefa $criarTarefa): JsonResponse
    {
        $dto = new CriarTarefaDTO(
            title: $request->input('title'),
            description: $request->input('description') ?? null,
        );

        $res = $criarTarefa->executar($dto);

        return response()->json(TarefaPresenter::detalhe($res), 201);
    }

    public function atualizarTarefa(int $id, AtualizarTarefaRequest $request, AtualizarTarefa $atualizarTarefa): JsonResponse
    {
        $dto = new AtualizarTarefaDTO(
            id: $id,
            title: $request->input('title'),
            description: $request->input('description') ?? null,
            completed: $request->input('completed') ?? false,
        );

        $response = $atualizarTarefa->executar($dto);

        return response()->json(TarefaPresenter::detalhe($response), 200);
    }

    public function carregarTarefa(int $id, TarefaService $tarefaService): JsonResponse
    {
        try {
            $tarefa = $tarefaService->carregar($id);
            return response()->json(TarefaPresenter::detalhe($tarefa), 200);
        } catch (\DomainException $e) {
            return response()->json(['mensagem' => $e->getMessage()], 403);
        }
    }

    public function paginate(PaginateTarefaRequest $request, TarefaService $service): JsonResponse
    {
        $page    = max(1, (int) $request->query('page', 1));
        $perPage = max(1, min(100, (int) $request->query('per_page', 8)));
        $from = $request->query('created_from');
        $to   = $request->query('created_to');

        $filtros = [
            'busca'     => $request->query('busca'),
            'completed'    => $request->query('completed'),
            'created_from' => $from ? ($from . ' 00:00:00') : null,
            'created_to'   => $to   ? ($to   . ' 23:59:59') : null,
        ];

        $result = $service->paginate($filtros, $page, $perPage);

        return response()->json([
            'data' => TarefaPresenter::colecao($result->items),
            'links' => [
                'total'        => $result->total,
                'current_page' => $result->page,
                'per_page'     => $result->perPage,
                'last_page'    => $result->lastPage,
            ],
        ], 200);
    }

    public function concluirTarefa(int $id, ConcluirTarefa $concluirTarefa): JsonResponse
    {
        $dto = $concluirTarefa->executar($id);
        return response()->json(TarefaPresenter::detalhe($dto), 200);
    }

    public function reabrirTarefa(int $id, ReabrirTarefa $reabrirTarefa): JsonResponse
    {
        $dto = $reabrirTarefa->executar($id);
        return response()->json(TarefaPresenter::detalhe($dto), 200);
    }

    public function excluirTarefa(int $id, ExcluirTarefa $excluirTarefa): JsonResponse
    {
        $excluirTarefa->executar($id);
        return response()->json(null, 204);
    }
}
