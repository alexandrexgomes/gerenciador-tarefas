<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class PermissaoModel extends Model
{
    use SoftDeletes;

    protected $table = 'permissoes';
    public $timestamps = true;

    protected $fillable = ['nome', 'descricao'];
}
