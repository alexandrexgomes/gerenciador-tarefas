<?php

namespace App\Domain\Entities;

final class Usuario
{
    public const STATUS_INATIVO = 0;
    public const STATUS_ATIVO = 1;

    public function __construct(
        private ?int $id,
        private string $nome,
        private string $email,
        private string $password,
        private int $status
    ) {
        $this->definirNome($nome);
        $this->definirEmail($email);
    }

    public function id(): ?int
    {
        return $this->id;
    }
    public function nome(): string
    {
        return $this->nome;
    }
    public function email(): string
    {
        return $this->email;
    }
    public function password(): string
    {
        return $this->password;
    }
    public function status(): int
    {
        return $this->status;
    }

    public function definirNome(string $nome): void
    {
        $nome = trim($nome);
        if ($nome === '') throw new \InvalidArgumentException('Nome inválido.');
        $this->nome = $nome;
    }

    public function definirEmail(string $email): void
    {
        $email = mb_strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('E-mail inválido.');
        }
        $this->email = $email;
    }

    public function alterarEmail(string $novoEmail): void
    {
        if (!filter_var($novoEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Formato de e-mail inválido.');
        }
        $this->email = $novoEmail;
    }

    public static function novo(string $nome, string $email, string $password): self
    {
        return new self(
            id: null,
            nome: $nome,
            email: $email,
            password: $password,
            status: self::STATUS_ATIVO
        );
    }
}
