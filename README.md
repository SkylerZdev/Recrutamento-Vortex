# Vortex · Recrutamento

Aplicação de gestão de vagas, candidatos e inscrições em Laravel 13. A interface web usa Blade, CSS e JavaScript. A API JSON e os formulários compartilham validações e regras de negócio. O ambiente de entrega usa PostgreSQL 17. Para desenvolvimento local e testes, é possível usar SQLite.

## Executar localmente com SQLite

Requisitos: PHP 8.5 com PDO SQLite, mbstring e extensões exigidas pelo Composer, e Composer 2. Na raiz de um clone novo, execute:

```sh
composer install
php -r "copy('.env.example', '.env'); touch('database/database.sqlite');"
php artisan key:generate
php artisan migrate --seed
php artisan recrutamento:usuario
php artisan serve
```

Abra [localhost:8000](http://localhost:8000) e entre com a conta criada no comando interativo. A senha deve ter pelo menos 12 caracteres. Em uma instalação existente, preserve o arquivo `.env` e a chave atual. Os arquivos de CSS e JavaScript são servidos diretamente de `public/`.

## Executar com Docker e PostgreSQL

Requisitos: Docker Engine ou Docker Desktop com contêineres Linux e Compose v2.

1. Crie um arquivo `.env` na raiz, se ainda não existir. Defina `POSTGRES_PASSWORD` com uma senha de sua escolha e `APP_KEY` com uma chave Laravel. Se já executou a instalação local, mantenha a chave existente e acrescente apenas a senha do PostgreSQL.
2. Para gerar uma chave sem PHP local, execute `docker run --rm php:8.5-cli php -r "echo 'base64:', base64_encode(random_bytes(32)), PHP_EOL;"` e copie a saída para `APP_KEY`.
3. Execute os comandos abaixo e abra [localhost:8000](http://localhost:8000).

```sh
docker compose up --build -d
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan recrutamento:usuario
```

O serviço `app` usa Apache e aguarda o PostgreSQL passar no healthcheck. O banco utiliza o volume `pgdata`. A porta 8000 fica vinculada ao computador local. Use `docker compose down` para parar os serviços e preservar os dados. Nas próximas inicializações, use `docker compose up -d` e `docker compose exec app php artisan migrate` para aplicar migrations pendentes.

## Avaliar a aplicação

O seed acrescenta 26 vagas (quatro pausadas), 32 candidatos e duas inscrições por candidato a cada execução. Os dados de demonstração são independentes das contas de acesso.

- Em **Vagas**, crie uma vaga CLT, Pessoa Jurídica ou Freelancer. Em **Candidatos**, cadastre uma pessoa com e-mail único.
- Abra a vaga e informe o e-mail cadastrado em **Inscrever candidato**. O detalhe do candidato lista todas as suas vagas. A mesma pessoa pode se inscrever uma vez em cada vaga.
- Edite a situação da vaga para **Pausada**. As inscrições existentes continuam visíveis. Reabra a vaga para receber novas inscrições.
- Nas duas listas, combine filtros por ID, campos do cadastro e datas de criação e atualização. A paginação começa em 20 itens e aceita de 1 a 100.
- Exclua um registro na linha ou marque vários e use **Excluir seleção**. As chaves estrangeiras removem as inscrições associadas.

## API JSON

Use HTTP Basic com as mesmas credenciais da interface. Em um servidor exposto, use HTTPS. O exemplo solicita a senha no terminal:

```sh
curl -u "seu-email@example.org" "http://localhost:8000/api/vagas?paused=0&contract=CLT&per_page=10"
```

| Método | Rota | Operação |
| --- | --- | --- |
| GET | `/api/vagas`, `/api/candidatos` | Lista paginada com filtros |
| POST | `/api/vagas`, `/api/candidatos` | Criação |
| GET | `/api/vagas/{id}`, `/api/candidatos/{id}` | Detalhe e inscrições paginadas |
| PUT / PATCH | `/api/vagas/{id}`, `/api/candidatos/{id}` | Atualização completa ou parcial |
| DELETE | `/api/vagas/{id}`, `/api/candidatos/{id}` | Exclusão individual |
| DELETE | `/api/vagas/lote`, `/api/candidatos/lote` | Exclusão em massa com `{"ids":[1,2]}` |
| POST | `/api/vagas/{id}/inscricoes` | Inscrição com `{"email":"ana@example.org"}` |

Envie corpos JSON com `Content-Type: application/json`. Para criar uma vaga, envie `title`, `description` e `contract` (valores exatos: `CLT`, `Pessoa Jurídica`, `Freelancer`). O booleano `paused` é opcional e inicia falso. Para criar um candidato, envie `name` e `email`.

As listas aceitam esses campos na query string, além de `id`, `created_at`, `updated_at` (datas no formato `AAAA-MM-DD`), `page` e `per_page`. Use `paused=0` ou `paused=1`. A resposta traz `data`, `total`, `current_page`, `per_page` e links de navegação. Criações retornam 201, exclusões 204 e erros de validação 422. A inscrição usa transação e bloqueio da vaga no PostgreSQL. Uma chave primária composta impede inscrições duplicadas.

## Testes

```sh
php artisan test
# ou dentro do contêiner
docker compose exec app php artisan test
```

A suíte usa SQLite em memória. Inclui testes unitários da disponibilidade da vaga e testes de integração dos formulários, sessão, API, validações, filtros, paginação, inscrições e exclusões. As migrations ficam em `database/migrations`, as factories em `database/factories` e a população inicial em `database/seeders`.
