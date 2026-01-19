# Gerenciador de Tarefas — Laravel API + Vue (SPA) com Docker

Sistema completo para **gestão de tarefas**, composto por:

- **Back-end (Laravel API)** com autenticação **JWT**, ACL (**usuário ↔ papéis ↔ permissões**) e regras de negócio.
- **Front-end (Vue 3 + Vite)** para autenticação e gerenciamento de tarefas (listar/criar/editar/concluir/reabrir/excluir).

---

## Requisitos Gerais

- Docker para facilitar a execução do back-end e do front-end.
- Projeto estruturado com boas práticas (Laravel + Vue).
- Testes unitários no back-end para as principais regras.
- Autenticação por token (JWT) protegendo a API.
- ACL por permissões para ações sensíveis (ex.: criar/concluir/reabrir/excluir).

---

## Stack

### Back-end
- Laravel 12 (API REST)
- JWT (auth)
- MySQL 8 (via Docker)
- Testes: PHPUnit (SQLite em memória)

### Front-end
- Vue 3 + TypeScript
- Vite
- Pinia
- Bootstrap + Bootstrap Icons
- Axios
- SweetAlert2

---

## Portas e URLs (padrão do Docker Compose)

- **API (Laravel)**: `http://localhost:8081`
- **Base da API**: `http://localhost:8081/api`
- **Front (Vite dev server)**: `http://localhost:5174`
- **MySQL (host)**: `127.0.0.1:3310`

> Caso você tenha alterado as portas no `docker-compose.yml`, use as portas configuradas no seu projeto.

---

## Setup rápido

```bash
git clone https://github.com/alexandrexgomes/gerenciador-tarefas.git
cd gerenciador-tarefas

cp frontend/.env.example frontend/.env
cp .env.example .env

docker compose up -d --build

docker compose exec php composer install
docker compose exec php php artisan key:generate
docker compose exec php php artisan jwt:secret
docker compose exec php php artisan migrate:fresh --seed
```

- **Front:** `http://localhost:5174`  
- **API:** `http://localhost:8081/api`

---

# Setup passo a passo

## 1) Clonar o repositório

```bash
git clone https://github.com/alexandrexgomes/gerenciador-tarefas.git
cd gerenciador-tarefas
```

## 2) Criar o `.env` do Front (Vite)

Crie o arquivo `frontend/.env` a partir do exemplo:

```bash
cp frontend/.env.example frontend/.env
```

Conteúdo esperado:

```env
VITE_API_URL=http://localhost:8081/api
DEV=true
```

## 3) Subir os containers (API + MySQL + Front)

```bash
docker compose up -d --build
```

Isso sobe os serviços:

- `apache` (porta **8081**)
- `php`
- `mysql` (porta **3310** no host)
- `frontend` (porta **5174**)

> Na primeira subida, o serviço **frontend** executa `npm install` automaticamente (pode levar um pouco).
> Para acompanhar: `docker compose logs -f frontend`

## 4) Instalar dependências do Laravel (Composer)

```bash
docker compose exec php composer install
```

## 5) Criar `.env` do Laravel

```bash
cp .env.example .env
```

Ajuste o `.env` para MySQL do Docker:

```env
APP_URL=http://localhost:8081

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=db_task
DB_USERNAME=app
DB_PASSWORD=app
```

> Observação: o container `mysql` usa as credenciais definidas em `.env.docker`.

## 6) Gerar `APP_KEY`

```bash
docker compose exec php php artisan key:generate
```

## 7) Gerar segredo do JWT (se necessário)

Se você quiser gerar/atualizar o `JWT_SECRET` no `.env`:

```bash
docker compose exec php php artisan jwt:secret
```

## 8) Rodar migrations e seeders (cria dados iniciais)

```bash
docker compose exec php php artisan migrate:fresh --seed
```

Pronto:

- **Front:** `http://localhost:5174`
- **API:** `http://localhost:8081/api`

---

## Dados iniciais (seed)

Após rodar `php artisan migrate:fresh --seed`, o projeto cria automaticamente:

### ACL (papéis e permissões)
- Papéis:
  - **Administrador** (acesso total: usuários + tarefas)
  - **Usuario** (permissões básicas de tarefas)
- Permissões cadastradas na tabela `permissoes`
- Vínculos **papel ↔ permissão** na tabela `papel_permissao`
- Vínculos **usuário ↔ papel** na tabela `papel_usuario`

### Usuários de teste
- **Admin** (papel: Administrador)  
  email: `admin@tarefas.local`  
  senha: `admin123`

- **Usuário padrão** (papel: Usuario)  
  email: `usuario@tarefas.local`  
  senha: `usuario123`

- **Usuário inativo** (status 0)  
  email: `inativo@tarefas.local`  
  senha: `inativo123`  
  > O usuário inativo existe para validar regra de bloqueio/estado.

### Tarefas de exemplo

Também são criadas tarefas na tabela `tarefas` para facilitar testes no front e na API (com datas variáveis).

---

## Front-end (Vue) — execução via Docker

O front **já é iniciado pelo Docker Compose** no serviço `frontend`.

Ele executa automaticamente:

- `npm install`
- `npm run dev -- --host 0.0.0.0 --port 5174`

### Ver logs do front

```bash
docker compose logs -f frontend
```

---

## Endpoints principais (API)

### Autenticação
- `POST /api/login` → retorna token JWT
- `GET  /api/v1/auth/perfil` (Bearer Token)

### Usuários (protegido)
- `GET    /api/v1/usuarios`
- `GET    /api/v1/usuarios/paginate`
- `POST   /api/v1/usuarios/cadastrar`
- `GET    /api/v1/usuarios/carregar/{id}`
- `PUT    /api/v1/usuarios/atualizar/{id}`
- `DELETE /api/v1/usuarios/excluir/{id}`

### Papéis (protegido)
- `GET /api/v1/papeis`

### Tarefas (protegido)
- `GET    /api/v1/tarefas`
- `GET    /api/v1/tarefas/paginate`
- `POST   /api/v1/tarefas/criar`
- `GET    /api/v1/tarefas/carregar/{id}`
- `PUT    /api/v1/tarefas/atualizar/{id}`
- `PATCH  /api/v1/tarefas/concluir/{id}`
- `PATCH  /api/v1/tarefas/reabrir/{id}`
- `DELETE /api/v1/tarefas/excluir/{id}`

---

## Paginação e filtros (tarefas)

Endpoint:

- `GET /api/v1/tarefas/paginate`

Query params suportados:

- `page` (padrão: 1)
- `per_page` (padrão: 8 | máximo: 100)
- `busca` (busca em `title` e `description`)
- `completed` (`0` ou `1`)
- `created_from` (formato `YYYY-MM-DD`)
- `created_to` (formato `YYYY-MM-DD`)

---

## Regras e comportamentos implementados

- Autenticação JWT protegendo rotas versionadas (`/api/v1/*`).
- ACL: usuário pode ter **múltiplos papéis**, e papéis possuem permissões.
- Ações sensíveis são bloqueadas via permissões (retornando `403` quando não autorizado).
- CRUD completo de tarefas + ações de **concluir** e **reabrir**.

### Parte 2 — Evolução de Código Legado (refatoração do método `store`)

Este projeto atende ao item de refatoração do método legado (`store(Request $request)`), aplicando:

- **Validação** via `FormRequest` (`app/Http/Requests/CriarTarefaRequest.php`), evitando uso direto de `$request->title`.
- **Separação de responsabilidades**: controller apenas orquestra; regra de criação fica no **caso de uso** (`app/Application/UseCases/Tarefa/CriarTarefa.php`) com DTO.
- **Retorno HTTP adequado**:
  - `201 Created` ao criar a tarefa com sucesso (`POST /api/v1/tarefas/criar`).
  - `422 Unprocessable Entity` para erros de validação (padrão do Laravel) e regras de negócio.
- **Tratamento de erros centralizado** (Laravel 12) no `bootstrap/app.php`, padronizando respostas para cenários comuns (ex.: validação, não autorizado, não encontrado).

---

## Postman (Collection + Environment)

O repositório inclui arquivos prontos para importação no Postman:

- `postman/Gerenciador-Tarefas.postman_collection.json`
- `postman/API-GERENCIADOR-TAREFAS.postman_environment.json`

### Como usar

1. Importe a **Collection** e o **Environment** no Postman.
2. Selecione o environment **“API-GERENCIADOR-TAREFAS”** (ou o nome que aparecer após importar).
3. Execute o request **Login** (o token JWT é salvo automaticamente em `{{token}}`).
4. Execute os demais endpoints (já usam `Authorization: Bearer {{token}}`).

---

## Como rodar testes (Back-end)

Os testes usam **SQLite em memória**, então não dependem do MySQL:

```bash
docker compose exec php php artisan test
```

---

## Comandos úteis

### Ver logs
```bash
docker compose logs -f apache
docker compose logs -f php
docker compose logs -f mysql
docker compose logs -f frontend
```

### Entrar no container PHP
```bash
docker compose exec php bash
```

### Recriar banco e popular novamente
```bash
docker compose exec php php artisan migrate:fresh --seed
```

### Parar o ambiente
```bash
docker compose down
```

### Parar e remover volumes (zera MySQL e `node_modules` do volume)
```bash
docker compose down -v
```