<?php

namespace App\Http\Controllers;

use App\Application\DTOs\Usuario\CadastrarUsuarioDTO;
use App\Application\UseCases\Usuario\CadastrarUsuario;
use App\Http\Requests\RegistrarUsuarioRequest;
use Illuminate\Http\JsonResponse;
use App\Application\Services\UsuarioService;
use Illuminate\Http\Request;
use App\Application\UseCases\Usuario\ExcluirUsuario;
use App\Application\UseCases\Usuario\AtualizarUsuario;
use App\Application\DTOs\Usuario\AtualizarUsuarioDTO;
use App\Http\Requests\AtualizarUsuarioRequest;
use App\Presentation\Presenters\UsuarioPresenter;
use App\Domain\Services\UsuarioAutenticadoInterface;

class UsuarioController extends Controller
{
    public function __construct(
        private UsuarioAutenticadoInterface $usuarioAutenticado
    ) {}

    public function index(UsuarioService $usuarioService): JsonResponse
    {
        $usuarios = $usuarioService->listar();
        return response()->json(UsuarioPresenter::colecao($usuarios), 200);
    }

    public function cadastrarUsuario(RegistrarUsuarioRequest $request, CadastrarUsuario $cadastrarUsuario): JsonResponse
    {
        $usuarioId = $this->usuarioAutenticado->id();

        $dto = new CadastrarUsuarioDTO(
            id: $usuarioId,
            nome: $request->input('nome'),
            email: $request->input('email'),
            password: $request->input('password'),
            papeis_ids: $request->input('papeis_ids', [])
        );

        try {
            $usuario = $cadastrarUsuario->executar($dto);
            return response()->json(UsuarioPresenter::detalhe($usuario), 201);
        } catch (\DomainException $e) {
            return response()->json(['mensagem' => $e->getMessage()], 403);
        }
    }

    public function carregarUsuario(int $id, UsuarioService $usuarioService): JsonResponse
    {
        try {
            $usuario = $usuarioService->find($id);
            return response()->json(UsuarioPresenter::lista($usuario), 200);
        } catch (\DomainException $e) {
            return response()->json(['mensagem' => $e->getMessage()], 403);
        }
    }

    public function paginate(Request $request, UsuarioService $usuarioService): JsonResponse
    {
        $page    = max(1, (int) $request->query('page', 1));
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));

        $filtros = [
            'busca'      => $request->query('busca'),
            'status'     => $request->query('status')
        ];

        $result = $usuarioService->paginate($filtros, $page, $perPage);

        return response()->json([
            'data' => UsuarioPresenter::colecao($result->items),
            'links' => [
                'total'        => $result->total,
                'current_page' => $result->page,
                'per_page'     => $result->perPage,
                'last_page'    => $result->lastPage,
            ],
        ], 200);
    }

    public function excluirUsuario($id, ExcluirUsuario $excluirUsuario): JsonResponse
    {
        $excluirUsuario->executar($id);
        return response()->json(null, 204);
    }

    public function atualizarUsuario(AtualizarUsuarioRequest $request, int $id, AtualizarUsuario $atualizarUsuario)
    {
        $dto = new AtualizarUsuarioDTO(
            id: $id,
            nome: $request->input('nome'),
            email: $request->input('email'),
            password: $request->input('password'),
            status: $request->input('status'),
            papeis_ids: $request->input('papeis_ids', [])
        );

        $atualizarUsuario->executar($dto);

        return response()->json([
            'mensagem' => 'Usuário atualizado com sucesso.'
        ], 200);
    }
}
