# ==============================================================================
# DRS ERP — Developer CLI
# Empresa: E2S Systems
# ==============================================================================
# Uso: make <target>
# Para ver todos os comandos disponíveis: make help
# ==============================================================================

.DEFAULT_GOAL := help
.PHONY: help up up-obs down stop restart build logs logs-backend logs-frontend \
        art migrate seed fresh tinker bash bash-db bash-bi bash-redis \
        test test-filter test-coverage lint analyse swagger \
        npm-install npm-build bi-shell setup

# ------------------------------------------------------------------------------
# Variáveis
# ------------------------------------------------------------------------------
DC         := docker compose
BACKEND    := $(DC) exec backend
FRONTEND   := $(DC) exec frontend
BI         := $(DC) exec bi-service

# Filtro opcional para comandos. Uso: make <target> f="example"
f ?=

# ==============================================================================
## Docker
# ==============================================================================

up: ## Inicia todos os containers em background
	$(DC) up -d

up-obs: ## Inicia todos os containers + stack de observabilidade (Prometheus + Grafana)
	$(DC) --profile observability up -d

down: ## Para e remove containers (mantém volumes)
	$(DC) down

down-v: ## Para e remove containers + volumes (perde dados)
	$(DC) down -v

stop: ## Para os containers (preserva estado)
	$(DC) stop

restart: ## Reinicia todos os containers
	$(DC) restart

reset: ## Destrói tudo (containers, volumes, dados) e recria o ambiente do zero
	@echo "⚠️  Isso vai apagar TODOS os dados locais (banco, redis, kafka)."
	@echo "Pressione CTRL+C para cancelar ou ENTER para continuar..."
	@read _
	$(DC) down -v --remove-orphans
	@$(MAKE) setup

build: ## Reconstrói as imagens sem cache
	$(DC) build --no-cache

logs: ## Exibe logs de todos os containers (streaming)
	$(DC) logs -f

logs-backend: ## Exibe logs apenas do backend
	$(DC) logs -f backend

logs-frontend: ## Exibe logs apenas do frontend
	$(DC) logs -f frontend

logs-bi: ## Exibe logs apenas do bi-service
	$(DC) logs -f bi-service

# ==============================================================================
## Backend (Laravel)
# ==============================================================================

art: ## Executa um comando Artisan. Uso: make art f="route:list"
	$(BACKEND) php artisan $(f)

migrate: ## Executa as migrations pendentes
	$(BACKEND) php artisan migrate --ansi

seed: ## Executa os seeders
	$(BACKEND) php artisan db:seed --ansi

fresh: ## Recria o banco do zero com migrations + seed
	$(BACKEND) php artisan migrate:fresh --seed --ansi

make: ## Cria um novo recurso. Uso: make make f="controller UserController"
	$(BACKEND) php artisan make:$(f)

tinker: ## Abre o REPL Tinker
	$(BACKEND) php artisan tinker

swagger: ## Gera a documentação OpenAPI via Scramble
	$(BACKEND) php artisan scramble:generate
	@echo "Documentação disponível em: http://localhost:8000/docs/api"

queue: ## Inicia o worker de filas
	$(BACKEND) php artisan queue:work --ansi

# ==============================================================================
## Testes (Pest PHP)
# ==============================================================================

test: ## Executa a suíte completa de testes
	$(BACKEND) composer test

test-filter: ## Executa testes por nome. Uso: make test-filter f="UserTest"
	$(BACKEND) php artisan test --filter=$(f)

test-coverage: ## Gera relatório de cobertura de testes
	$(BACKEND) php artisan test --coverage

# ==============================================================================
## Qualidade de Código
# ==============================================================================

lint: ## Executa o Laravel Pint (auto-corrige estilo)
	$(BACKEND) ./vendor/bin/pint --ansi

lint-dry: ## Verifica estilo sem aplicar correções
	$(BACKEND) ./vendor/bin/pint --test --ansi

#analyse: ## Executa análise estática com Larastan/PHPStan
#	$(BACKEND) ./vendor/bin/phpstan analyse --ansi

# ==============================================================================
## Frontend (Nuxt 4)
# ==============================================================================

npm-install: ## Instala dependências do frontend
	$(FRONTEND) npm install

npm-build: ## Build de produção do frontend
	$(FRONTEND) npm run build

# ==============================================================================
## Shells / Acesso Direto
# ==============================================================================

bash: ## Abre bash no container do backend
	$(BACKEND) sh

bash-db: ## Abre psql no container do PostgreSQL
	$(DC) exec postgres psql -U $${DB_USERNAME:-postgres} -d $${DB_DATABASE:-postgres}

bash-bi: ## Abre bash no container do bi-service
	$(BI) sh

bash-redis: ## Abre redis-cli
	$(DC) exec redis redis-cli

# ==============================================================================
## Setup
# ==============================================================================

setup: ## Configura o projeto do zero para novos desenvolvedores
	@bash ./bin/setup.sh

# ==============================================================================
## Ajuda
# ==============================================================================

help: ## Exibe este menu de ajuda
	@awk 'BEGIN {FS = ":.*##"; printf "\n\033[1;37mDRS ERP — Comandos Disponíveis\033[0m\n\n"} \
		/^[a-zA-Z_\-]+:.*?##/ { printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2 } \
		/^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) }' \
		$(MAKEFILE_LIST)
	@echo ""