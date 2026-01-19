<?php

namespace App\Providers;

use App\Infrastructure\Adapters\AutorizacaoBanco;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /** ---------------------------
         *  Services
         *  ------------------------- */
        $this->app->singleton(\App\Domain\Services\Hash\ServiceHashInterface::class, \App\Infrastructure\Adapters\Hash\ServiceHash::class);
        $this->app->singleton(\App\Domain\Services\ProvedorTokenInterface::class, \App\Infrastructure\Adapters\JwtProvedorToken::class);

        $this->app->bind(
            \App\Domain\Services\AutorizacaoInterface::class,
            \App\Infrastructure\Adapters\AutorizacaoBanco::class
        );

        $this->app->bind(
            \App\Domain\Services\ClockInterface::class,
            \App\Infrastructure\Adapters\CarbonClock::class
        );

        /** ---------------------------
         *  Repositories (Domain)
         *  ------------------------- */
        $this->app->bind(\App\Domain\Services\TransactionManagerInterface::class, \App\Infrastructure\Persistence\Eloquent\Transaction::class);
        $this->app->bind(\App\Domain\Repositories\UsuarioRepositoryInterface::class, \App\Infrastructure\Persistence\Eloquent\Repositories\UsuarioRepository::class);
        $this->app->bind(\App\Application\Queries\UsuarioQueryRepositoryInterface::class, \App\Infrastructure\Persistence\Eloquent\QueryRepositories\UsuarioQueryRepository::class);
        $this->app->bind(\App\Application\Queries\TarefaQueryRepositoryInterface::class, \App\Infrastructure\Persistence\Eloquent\QueryRepositories\TarefaQueryRepository::class);
        $this->app->bind(\App\Domain\Repositories\TarefaRepositoryInterface::class, \App\Infrastructure\Persistence\Eloquent\Repositories\TarefaRepository::class);
        $this->app->bind(\App\Application\Queries\PapelQueryRepositoryInterface::class, \App\Infrastructure\Persistence\Eloquent\QueryRepositories\PapelQueryRepository::class);
        $this->app->bind(\App\Domain\Services\UsuarioAutenticadoInterface::class, \App\Infrastructure\Adapters\UsuarioAutenticadoAuthAdapter::class);
        $this->app->bind(\App\Domain\Services\AutorizacaoInterface::class, \App\Infrastructure\Adapters\AutorizacaoBanco::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
