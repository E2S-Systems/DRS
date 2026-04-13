# 🏎️ DRS (Dynamic Resource System)

Desenvolvido por E2S Systems

## 📌 Sobre o Projeto

O DRS é um ERP SaaS verticalizado de alta performance, projetado especificamente para o varejo de comunicação visual. Em vez de um sistema monolítico rígido, o DRS utiliza uma arquitetura orientada a eventos para reduzir o "arrasto operacional" de empresas com múltiplas filiais, integrando gestão transacional (Vendas, Estoque) com serviços analíticos.

## 🏗️ Arquitetura (Monorepo)

Este repositório consolida todo o ecossistema DRS, estruturado sob o padrão de Monorepo para garantir consistência de deploy e versionamento atômico:

```
drs-erp/
├── backend/                # Laravel 12 Application (Core)
├── bi-service/             # Python FastAPI Application (Analytics)
├── bin/                    # Scripts de automação (setup.sh)
├── docs/                   # Project Documentation (Markdown, Diagrams)
├── frontend/               # Nuxt 4 Application (SPA)
├── infra/                  # Manifestos IaC (Kubernetes, Prometheus, Grafana)
├── docker-compose.yml      # Orquestração local de desenvolvimento
└── Makefile                # CLI de comandos do projeto
```

## 🛠️ Stack Tecnológica

- **Apresentação:** Vue.js 3, Nuxt 4, TailwindCSS, Pinia.
- **Regra de Negócio:** PHP 8.4, Laravel 12, Spatie QueryBuilder/Permission/MediaLibrary.
- **Inteligência e Dados:** Python 3.11, FastAPI, Pandas (Polars), LangGraph.
- **Persistência & Mensageria:** PostgreSQL 16, Redis 7, Apache Kafka.

## 🚀 Como Iniciar (Ambiente de Desenvolvimento)

Para garantir paridade entre os ambientes da equipa, não instale dependências na sua máquina local. Toda a infraestrutura e instalações são orquestradas por dentro dos containers Docker.

**Pré-requisitos:** Docker e Git instalados na máquina.

### Setup em 3 comandos

```bash
git clone git@github.com:E2S-Systems/DRS.git
cd DRS
make setup
```

O `make setup` cuida de tudo automaticamente: sobe os containers, aguarda os serviços ficarem saudáveis, instala dependências PHP e Node, gera a `APP_KEY`, executa migrations e seeders.

> **Credenciais padrão:** `admin@drs.systems` / `drs@123456`

### Resetar o ambiente do zero

Caso precise recriar o ambiente completamente (apaga todos os dados locais):

```bash
make reset
```

## 🌐 Acessos Locais

| Serviço | URL |
|---|---|
| Frontend (SPA) | http://localhost:3000 |
| Backend API | http://localhost:8000 |
| API Docs (Scramble) | http://localhost:8000/docs/api |
| BI Service Docs | http://localhost:8001/docs |
| PostgreSQL | localhost:5434 |
| Redis | localhost:6379 |

Para subir a stack de observabilidade (Prometheus + Grafana):

```bash
make up-obs
```

| Serviço | URL |
|---|---|
| Prometheus | http://localhost:9090 |
| Grafana | http://localhost:3001 |

## ⚙️ Comandos Disponíveis

O projeto expõe um CLI unificado via `Makefile`. Para ver todos os comandos disponíveis:

```bash
make help
```

Referência rápida dos comandos mais usados:

| Comando            | Descrição |
|--------------------|---|
| `make up`          | Inicia os containers |
| `make down`        | Para e remove os containers |
| `make stop`        | Para os containers (preserva estado) |
| `make restart`     | Reinicia os containers |
| `make reset`       | Destrói tudo e recria o ambiente do zero |
| `make art q="..."` | Executa um comando Artisan |
| `make migrate`     | Executa migrations pendentes |
| `make seed`        | Executa os seeders |
| `make fresh`       | Recria o banco do zero com seed |
| `make test`        | Executa a suíte completa de testes |
| `make bash`        | Abre bash no container do backend |
| `make setup`       | Configura o projeto do zero |
| `make help`        | Exibe todos os comandos disponíveis |

## 📚 Documentação e Diretrizes

Antes de iniciar qualquer desenvolvimento ou abrir um Pull Request, é obrigatória a leitura das diretrizes de engenharia do projeto.

[📗 Technical Design Document (TDD)](docs/technical-design-document.md)

[📘 Project Governance](docs/project-governance.md)

[📙 Development Guide](docs/development-guide.md)

---

_Propriedade Intelectual - E2S Systems © 2026_
