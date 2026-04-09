#!/usr/bin/env bash
# ==============================================================================
# DRS ERP — Setup Script
# Empresa: E2S Systems
# ==============================================================================
# Configura o ambiente de desenvolvimento local do zero.
# Este script é idempotente: pode ser executado mais de uma vez sem efeitos colaterais.
#
# Uso: bash ./bin/setup.sh
#      make setup
# ==============================================================================

set -euo pipefail

# ------------------------------------------------------------------------------
# Cores e helpers de output
# ------------------------------------------------------------------------------
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
BOLD='\033[1m'
RESET='\033[0m'

info()    { echo -e "${BLUE}ℹ${RESET}  $1"; }
success() { echo -e "${GREEN}✔${RESET}  $1"; }
warn()    { echo -e "${YELLOW}⚠${RESET}  $1"; }
error()   { echo -e "${RED}✖${RESET}  $1"; exit 1; }
step()    { echo -e "\n${BOLD}▶ $1${RESET}"; }

# ------------------------------------------------------------------------------
# Banner
# ------------------------------------------------------------------------------
echo ""
echo -e "${BOLD}╔════════════════════════════════════════╗${RESET}"
echo -e "${BOLD}║        DRS ERP — Project Setup         ║${RESET}"
echo -e "${BOLD}║           E2S Labs · $(date +%Y)              ║${RESET}"
echo -e "${BOLD}╚════════════════════════════════════════╝${RESET}"
echo ""

# ------------------------------------------------------------------------------
# Passo 0: Verificar dependências do host
# ------------------------------------------------------------------------------
step "Verificando dependências do sistema"

command -v docker >/dev/null 2>&1 \
    || error "Docker não encontrado. Instale em: https://docs.docker.com/get-docker/"
success "Docker encontrado: $(docker --version | awk '{print $3}' | tr -d ',')"

docker compose version >/dev/null 2>&1 \
    || error "Docker Compose (v2) não encontrado. Verifique sua instalação do Docker."
success "Docker Compose encontrado: $(docker compose version --short)"

command -v git >/dev/null 2>&1 \
    || error "Git não encontrado."
success "Git encontrado: $(git --version | awk '{print $3}')"

# ------------------------------------------------------------------------------
# Passo 1: Variáveis de ambiente
# ------------------------------------------------------------------------------
step "Configurando variáveis de ambiente"

if [ ! -f .env ]; then
    cp .env.example .env
    success ".env criado a partir do .env.example"
    warn "Revise o arquivo .env e ajuste as variáveis antes de continuar se necessário."
else
    warn ".env já existe — pulando criação (não foi sobrescrito)"
fi

# ------------------------------------------------------------------------------
# Passo 2: Subir infraestrutura base
# ------------------------------------------------------------------------------
step "Iniciando containers Docker"

docker compose up -d
success "Containers iniciados"

# ------------------------------------------------------------------------------
# Passo 3: Aguardar serviços ficarem saudáveis
# ------------------------------------------------------------------------------
step "Aguardando serviços ficarem prontos"

wait_for_service() {
    local service=$1
    local max_attempts=30
    local attempt=0

    info "Aguardando '$service'..."

    while [ $attempt -lt $max_attempts ]; do
        status=$(docker compose ps --format json "$service" 2>/dev/null \
            | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('Health',''))" 2>/dev/null \
            || echo "")

        if [ "$status" = "healthy" ]; then
            success "'$service' está saudável"
            return 0
        fi

        attempt=$((attempt + 1))
        sleep 2
    done

    error "Timeout aguardando '$service'. Verifique os logs: docker compose logs $service"
}

wait_for_service postgres
wait_for_service redis
wait_for_service kafka

# ------------------------------------------------------------------------------
# Passo 4: Backend — Dependências PHP
# ------------------------------------------------------------------------------
step "Instalando dependências PHP (Composer)"

docker compose exec backend composer install -n -o
success "Dependências PHP instaladas"

# ------------------------------------------------------------------------------
# Passo 5: Backend — Chave da aplicação
# ------------------------------------------------------------------------------
step "Configurando chave da aplicação Laravel"

APP_KEY=$(docker compose exec backend php artisan key:show --no-ansi 2>/dev/null | tr -d '[:space:]' || echo "")

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    docker compose exec backend php artisan key:generate --ansi
    success "APP_KEY gerada"
else
    warn "APP_KEY já configurada — pulando"
fi

# ------------------------------------------------------------------------------
# Passo 6: Banco de dados — Migrations + Seed
# ------------------------------------------------------------------------------
step "Executando migrations e seeders"

docker compose exec backend php artisan migrate:fresh --ansi
success "Migrations executadas"

docker compose exec backend php artisan db:seed --ansi
success "Seeders executados"

# ------------------------------------------------------------------------------
# Passo 7: Frontend — Dependências Node
# ------------------------------------------------------------------------------
step "Instalando dependências do frontend (npm)"

docker compose exec frontend npm install
success "Dependências do frontend instaladas"

# ------------------------------------------------------------------------------
# Sumário final
# ------------------------------------------------------------------------------
echo ""
echo -e "${BOLD}${GREEN}╔════════════════════════════════════════╗${RESET}"
echo -e "${BOLD}${GREEN}║           Setup Concluído!             ║${RESET}"
echo -e "${BOLD}${GREEN}╚════════════════════════════════════════╝${RESET}"
echo ""
echo -e "  ${BOLD}Frontend:${RESET}       http://localhost:3000"
echo -e "  ${BOLD}Backend API:${RESET}    http://localhost:8000"
echo -e "  ${BOLD}Swagger/Docs:${RESET}   http://localhost:8000/docs/api"
echo -e "  ${BOLD}BI Service:${RESET}     http://localhost:8001/docs"
echo -e "  ${BOLD}PostgreSQL:${RESET}     localhost:5434"
echo -e "  ${BOLD}Redis:${RESET}          localhost:6379"
echo ""
echo -e "  ${BOLD}Credenciais padrão:${RESET} admin@drs.systems / drs@123456"
echo ""
echo -e "  ${YELLOW}Para subir a stack de observabilidade (Prometheus + Grafana):${RESET}"
echo -e "  make up-obs"
echo ""
echo -e "  ${BLUE}Para ver todos os comandos disponíveis:${RESET}"
echo -e "  make help"
echo ""