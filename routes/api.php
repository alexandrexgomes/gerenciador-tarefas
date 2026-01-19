<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutenticacaoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PapelController;
use App\Http\Controllers\TarefaController;

Route::post('/login', [AutenticacaoController::class, 'login']);

Route::group(
    [
        'prefix' => 'v1',
        'middleware' => ['auth:api']
    ],
    function () {
        Route::get('/auth/perfil', [AutenticacaoController::class, 'perfil']);

        Route::prefix('usuarios')->group(function () {
            Route::get('/', [UsuarioController::class, 'index']);
            Route::post('/cadastrar', [UsuarioController::class, 'cadastrarUsuario']);
            Route::put('atualizar/{id}', [UsuarioController::class, 'atualizarUsuario']);
            Route::delete('excluir/{id}', [UsuarioController::class, 'excluirUsuario']);
            Route::get('/carregar/{id}', [UsuarioController::class, 'carregarUsuario']);
            Route::get('/paginate', [UsuarioController::class, 'paginate']);
        });

        Route::prefix('papeis')->group(function () {
            Route::get('/', [PapelController::class, 'index']);
        });

        Route::prefix('tarefas')->group(function () {
            Route::get('/', [TarefaController::class, 'index']);
            Route::post('/criar', [TarefaController::class, 'criarTarefa']);
            Route::get('/carregar/{id}', [TarefaController::class, 'carregarTarefa']);
            Route::get('/paginate', [TarefaController::class, 'paginate']);
            Route::put('/atualizar/{id}', [TarefaController::class, 'atualizarTarefa']);
            Route::patch('/concluir/{id}', [TarefaController::class, 'concluirTarefa']);
            Route::patch('/reabrir/{id}', [TarefaController::class, 'reabrirTarefa']);
            Route::delete('/excluir/{id}', [TarefaController::class, 'excluirTarefa']);
        });
    }
);
