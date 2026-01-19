<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Infrastructure\Persistence\Eloquent\Models\PermissaoModel;

class PapelModel extends Model
{
    use SoftDeletes;

    protected $table = 'papeis';
    public $timestamps = true;

    protected $fillable = ['nome', 'descricao'];

    public function permissoes(): BelongsToMany
    {
        return $this->belongsToMany(
            PermissaoModel::class,
            'papel_permissao',
            'papel_id',
            'permissao_id'
        );
    }
}
