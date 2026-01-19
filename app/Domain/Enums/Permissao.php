<?php

namespace App\Domain\Enums;

enum Permissao: string
{

    case CRIAR_USUARIO = 'usuario.criar';
    case ATUALIZAR_USUARIO = 'usuario.atualizar';
    case EXCLUIR_USUARIO = 'usuario.excluir';


    case CRIAR_TAREFA = 'tarefa.criar';
    case LISTAR_PROPRIAS = 'tarefa.listar_proprias';
    case VER_PROPRIAS    = 'tarefa.ver_proprias';

    case LISTAR_TODAS = 'tarefa.listar_todas';
    case VER_TODAS    = 'tarefa.ver_todas';

    case ATUALIZAR_TAREFA = 'tarefa.atualizar';
    case EXCLUIR_TAREFA   = 'tarefa.excluir';

    case CONCLUIR_TAREFA = 'tarefa.concluir';
    case REABRIR_TAREFA  = 'tarefa.reabrir';
}
