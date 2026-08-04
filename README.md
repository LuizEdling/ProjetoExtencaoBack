# BorderCare

API backend do **BorderCare**, sistema de gestão para clínicas veterinárias e abrigos de animais. O projeto centraliza o cadastro de animais, o fluxo clínico (estados de atendimento), adoções, adotantes, lembretes recorrentes, controle de gastos e relatórios com exportação em PDF.

## Tecnologias

- [PHP](https://www.php.net/) 8.3+
- [Laravel](https://laravel.com/) 13
- [Laravel Sanctum](https://laravel.com/docs/sanctum) (autenticação via token)
- [DomPDF](https://github.com/barryvdh/laravel-dompdf) (geração de contratos e relatórios)
- SQLite (desenvolvimento) ou MySQL/PostgreSQL (produção)

## Configuração do ambiente de desenvolvimento

### Pré-requisitos

- PHP 8.3 ou superior, com extensões: `pdo`, `sqlite3` (ou driver do banco escolhido), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) 18+ e npm (para assets com Vite)

### Passo a passo

1. **Clone o repositório**

   ```bash
   git clone https://github.com/LuizEdling/ProjetoExtencaoBack.git
   cd ProjetoExtencaoBack
   ```

2. **Instale as dependências e configure o ambiente**

   O projeto inclui um script de setup no Composer:

   ```bash
   composer setup
   ```

   Esse comando executa, em sequência: `composer install`, cria o `.env` a partir do `.env.example`, gera a `APP_KEY`, roda as migrations, instala dependências npm e compila os assets.

   **Alternativa manual:**

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   touch database/database.sqlite
   php artisan migrate
   npm install
   npm run build
   ```

3. **Ajuste o arquivo `.env`**

   Principais variáveis para desenvolvimento local:

   ```env
   APP_NAME=BorderCare
   APP_URL=http://localhost:8000
   APP_TIMEZONE=America/Sao_Paulo

   DB_CONNECTION=sqlite
   ```

   Para usar MySQL ou PostgreSQL, descomente e preencha as variáveis `DB_*` correspondentes no `.env.example`.

4. **Popule o banco com dados de exemplo (opcional)**

   ```bash
   php artisan db:seed
   ```

   Usuário padrão criado pelo seeder:

   | Campo    | Valor              |
   |----------|--------------------|
   | E-mail   | `test@example.com` |
   | Senha    | `password`         |

## Como rodar o projeto localmente

### Modo desenvolvimento (recomendado)

Inicia o servidor PHP, fila, logs e Vite em paralelo:

```bash
composer dev
```

Serviços disponíveis:

| Serviço        | URL                          |
|----------------|------------------------------|
| API            | http://localhost:8000        |
| Health check   | http://localhost:8000/up     |
| Endpoints API  | http://localhost:8000/api/*  |

### Modo simples (apenas API)

```bash
php artisan serve
```

### Testes

```bash
composer test
```

ou:

```bash
php artisan test
```

### Integração com o frontend

A API expõe rotas em `/api`. O CORS está configurado para o frontend em `http://localhost:5173` (Vite). Ao usar outra origem em produção, atualize `config/cors.php`.

Autenticação: `POST /api/login` retorna um token Sanctum; envie-o no header `Authorization: Bearer {token}` nas demais requisições.

## Principais funcionalidades da API

- Autenticação (`login` / `logout`)
- Cadastro e gestão de animais (protocolo, estados clínicos, microchip, local de resgate)
- Adotantes e adoções (com geração de contrato em PDF)
- Painel operacional com resumos e fila de atendimento
- Lembretes com recorrência configurável
- Gastos e doações
- Relatórios com dashboard e exportação em PDF

## Deploy

Este repositório contém apenas o backend. Para publicar em produção:

1. Configure o servidor com PHP 8.3+, Composer e um banco de dados (MySQL/PostgreSQL recomendados).
2. Defina variáveis de ambiente de produção:

   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://seu-dominio.com
   ```

3. Instale dependências e otimize a aplicação:

   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. Aponte o document root do servidor web (Nginx/Apache) para a pasta `public/`.
5. Configure permissões de escrita em `storage/` e `bootstrap/cache/`.
6. Adicione a URL do frontend em `config/cors.php` nas origens permitidas.

Para hospedagem gerenciada (Laravel Forge, Railway, Render etc.), siga a documentação do provedor adaptando os comandos acima.

## Licença

Este projeto está licenciado sob a [MIT License](https://opensource.org/licenses/MIT).

## Créditos

Projeto desenvolvido pela equipe:

- Luiz Henrique Ribas Edling
- Danton Bernardo Oliveira de Souza
- Felipe Gorgo Kiçula
- Rubens Santana
