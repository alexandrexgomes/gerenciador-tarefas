<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarefaModel extends Model
{
    use SoftDeletes;

    protected $table = 'tarefas';

    protected $fillable = [
        'title',
        'description',
        'completed',
    ];
}
