# Documentação do Projeto

## Visão Geral

Este projeto é uma API para sugestão, aprovação e ranqueamento de músicas,
construída com Laravel. O backend é estruturado em camadas
(Domain, Application, Infrastructure, Presentation) e utiliza Docker
para facilitar o setup do ambiente.

## Estrutura de Pastas

- **app/**: Código principal dividido em camadas (Domain, Application, Infrastructure, Presentation)
  - **Domain/**: Entidades, Value Objects, Repositórios (interfaces) e Serviços de domínio
  - **Application/**: Casos de uso, DTOs e Serviços de aplicação
  - **Infrastructure/**: Implementações de repositórios, serviços externos e configuração
  - **Presentation/**: Controllers, Requests e Resources
- **bootstrap/**: Arquivo de inicialização da aplicação
- **config/**: Configurações da aplicação
- **routes/**: Definição das rotas (api.php, web.php)
- **database/**: Migrations, seeds e factories
- **resources/**: Views e assets
- **public/**: Arquivos públicos e ponto de entrada da aplicação
- **tests/**: Testes automatizados

## Decisões Técnicas

- **Laravel**: Framework robusto, modular e com suporte a testes, autenticação e filas.
- **Camadas**: Separação clara entre regras de negócio, infraestrutura e apresentação.
- **Docker**: Facilita o setup do ambiente.
- **Redis**: Utilizado para cache e filas.
- **Sanctum**: Autenticação de API simples e segura.
- **Pest**: Framework de testes moderno e simples.

## Como Executar o Ambiente

### Pré-requisitos

- Docker e Docker Compose

### Passo a Passo (usando Docker)

1. Clone o repositório
2. Copie `.env.example` para `.env` e ajuste as variáveis conforme necessário
3. Execute:
    ```bash
    docker run --rm -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
    ```
    ```bash
    ./vendor/bin/sail up -d && \
    ./vendor/bin/sail artisan key:generate && \
    ./vendor/bin/sail artisan migrate
   ```

## Endpoints

### Autenticação

- `POST /api/login`
    - Parâmetros: `email`, `password`
    - Retorna: token de autenticação

### Público

- `GET /api/songs`
    - Lista músicas sugeridas
    - Throttle: 60 req/min
- `POST /api/songs/suggest`
    - Sugere uma nova música
    - Parâmetros: título, artista, link do YouTube
    - Throttle: 1 req/min

### Protegido (dashboard, requer token)

- `GET /api/dashboard/songs`
    - Lista todas as músicas
- `GET /api/dashboard/songs/{id}`
    - Detalhes de uma música
- `POST /api/dashboard/songs`
    - Adiciona uma música
- `PATCH /api/dashboard/songs/{id}`
    - Atualiza uma música
- `DELETE /api/dashboard/songs/{id}`
    - Remove uma música
- `PATCH /api/dashboard/songs/{id}/approve`
    - Aprova uma sugestão
- `PATCH /api/dashboard/songs/{id}/reject`
    - Rejeita uma sugestão

### Observações

- Endpoints protegidos exigem autenticação via token (Sanctum)
- Limites de requisição (throttle) aplicados conforme rota
- Respostas seguem padrão JSON

## Testes

Para rodar os testes:

```
./vendor/bin/sail test --coverage
```
