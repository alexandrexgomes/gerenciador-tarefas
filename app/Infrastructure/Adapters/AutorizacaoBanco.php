<?php

namespace App\Infrastructure\Adapters;

use Illuminate\Support\Facades\DB;
use App\Domain\Enums\Permissao;
use App\Domain\Services\AutorizacaoInterface;
use App\Infrastructure\Persistence\Eloquent\Models\PapelModel;
use App\Domain\Services\UsuarioAutenticadoInterface;

final class AutorizacaoBanco implements AutorizacaoInterface
{
    private ?int $usuarioIdCache = null;
    private ?bool $isAdminCache = null;

    /**
     * @var array<string,true>|null
     */
    private ?array $permissoesUsuarioCache = null;

    private static ?int $papelAdminIdCache = null;

    public function __construct(
        private UsuarioAutenticadoInterface $usuarioAutenticado
    ) {}

    public function temPermissao(Permissao $permissao): bool
    {
        $usuarioId = $this->getUsuarioId();

        if ($this->isAdmin($usuarioId)) {
            return true;
        }

        $this->carregarPermissoesSeNecessario($usuarioId);

        return isset($this->permissoesUsuarioCache[$permissao->value]);
    }

    private function carregarPermissoesSeNecessario(int $usuarioId): void
    {
        if ($this->permissoesUsuarioCache !== null) {
            return;
        }

        $nomes = DB::table('papel_usuario AS pu')
            ->join('papeis AS p', 'p.id', '=', 'pu.papel_id')
            ->join('papel_permissao AS pp', 'pp.papel_id', '=', 'p.id')
            ->join('permissoes AS pe', 'pe.id', '=', 'pp.permissao_id')
            ->where('pu.usuario_id', $usuarioId)
            ->whereNull('p.deleted_at')
            ->whereNull('pe.deleted_at')
            ->pluck('pe.nome')
            ->all();

        $this->permissoesUsuarioCache = array_fill_keys($nomes, true);
    }

    private function isAdmin(int $usuarioId): bool
    {
        if ($this->isAdminCache !== null) {
            return $this->isAdminCache;
        }

        $papelAdminId = $this->getPapelAdminId();
        if (!$papelAdminId) {
            return $this->isAdminCache = false;
        }

        $existe = DB::table('papel_usuario')
            ->where('usuario_id', $usuarioId)
            ->where('papel_id', $papelAdminId)
            ->exists();

        return $this->isAdminCache = ($existe === true);
    }

    private function getPapelAdminId(): ?int
    {
        if (self::$papelAdminIdCache !== null) {
            return self::$papelAdminIdCache;
        }

        $papelAdmin = PapelModel::where('nome', 'Administrador')->first();
        if (!$papelAdmin) {
            return null;
        }

        return self::$papelAdminIdCache = (int) $papelAdmin->id;
    }

    public function getUsuarioId(): int
    {
        if ($this->usuarioIdCache !== null) {
            return $this->usuarioIdCache;
        }

        return $this->usuarioIdCache = $this->usuarioAutenticado->id();
    }
}
