<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UsuarioModel extends Authenticatable implements JWTSubject
{
    use SoftDeletes;

    protected $table = 'usuarios';
    public $timestamps = true;

    protected $fillable = [
        'nome',
        'email',
        'password',
        'status'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function papeis(): BelongsToMany
    {
        return $this->belongsToMany(
            PapelModel::class,
            'papel_usuario',
            'usuario_id',
            'papel_id'
        );
    }
}
