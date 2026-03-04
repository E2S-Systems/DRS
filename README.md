# 🏎️ DRS (Dynamic Resource System)

Desenvolvido por E2S Systems

## 📌 Sobre o Projeto

O DRS é um ERP SaaS verticalizado de alta performance, projetado especificamente para o varejo de comunicação visual. Em vez de um sistema monolítico rígido, o DRS utiliza uma arquitetura orientada a eventos para reduzir o "arrasto operacional" de empresas com múltiplas filiais, integrando gestão transacional (Vendas, Estoque) com inteligência preditiva (Data/ML).

## 🏗️ Arquitetura (Monorepo)

Este repositório consolida todo o ecossistema DRS, estruturado sob o padrão de Monorepo para garantir consistência de deploy e versionamento atômico:
```
drs-erp/
├── backend/                # Laravel 11 Application (Core)
├── bi-service/             # Python FastAPI Application (Analytics)
├── docs/                   # Project Documentation (Markdown, Diagrams)
├── frontend/               # Nuxt 3 Application (SPA)
├── infra/                  # Manifestos IaC (Kubernetes, Docker, Prometheus)
└── docker-compose.yml      # Orquestração local de desenvolvimento
```

## 🛠️ Stack Tecnológica

- **Apresentação:** Vue.js 3, Nuxt 4, TailwindCSS, Pinia.

- **Regra de Negócio:** PHP 8.4, Laravel 12, Spatie Data/QueryBuilder.

- **Inteligência e Dados:** Python 3.11, FastAPI, Polars, LangChain.

- **Persistência & Mensageria:** PostgreSQL 16, Redis 7, Apache Kafka.

## 🚀 Como Iniciar (Ambiente de Desenvolvimento)

Para garantir paridade entre os ambientes da equipa, não instale dependências na sua máquina local. Toda a infraestrutura e instalações são orquestradas por dentro dos containers Docker.

### Passo 1: Clonar o repositório
```bash
git clone git@github.com:e2s-labs/drs-erp.git
cd drs-erp
```

### Passo 2: Configurar as Variáveis de Ambiente

Crie os ficheiros .env a partir dos exemplos fornecidos em cada serviço.
```bash
# Copiar o .env raiz
cp .env.example .env

# Copiar os .env dos serviços específicos
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
cp bi-service/.env.example bi-service/.env
```

> (Atenção: Solicite as credenciais sensíveis e passwords dos bancos de dados ao Tech Lead para preencher os ficheiros .env).

### Passo 3: Construir e Levantar a Infraestrutura

O comando abaixo irá baixar as imagens do SO, compilar os Dockerfiles e iniciar os serviços em segundo plano.
```bash
docker compose up -d --build
```

>(Nota: O nosso docker-compose.yml está programado para rodar composer install e npm install automaticamente no arranque. No entanto, se for a sua primeira vez ou ocorrer algum erro de sincronização, execute o Passo 4).

### Passo 4: Instalação Manual de Dependências e Setup do Laravel

Execute os comandos abaixo para garantir que o `vendor` e o `node_modules` estão perfeitamente alinhados, e para gerar a chave de criptografia do Laravel.
```bash
# 1. Instalar dependências do Backend (PHP)
docker compose exec backend composer install

# 2. Gerar a App Key do Laravel
docker compose exec backend php artisan key:generate

# 3. Rodar as Migrations e os Seeders (Criação do Banco de Dados e Permissões Base)
docker compose exec backend php artisan migrate --seed

# 4. Instalar dependências do Frontend (Node)
docker compose exec frontend npm install
```

## 🌐 Acessos Locais

Após o arranque dos containers, a plataforma estará disponível nos seguintes endereços na sua máquina:

- Frontend (SPA): http://localhost:3000

- Backend API: http://localhost:8000

- Documentação da API (Laravel/Scramble): http://localhost:8000/docs/api

- Documentação de Dados (Python/FastAPI): http://localhost:8001/docs

## 📚 Documentação e Diretrizes

Antes de iniciar qualquer desenvolvimento ou abrir um Pull Request, é obrigatória a leitura das diretrizes de engenharia do projeto.

[📗 Technical Design Document (TDD)](docs/technical-design-document.md)

[📘 Project Governance](docs/project-governance.md)

[📙 Development Guide](docs/development-guide.md)

_Confidencial - Propriedade Intelectual da E2S Labs © 2026_