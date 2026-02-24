# 🏎️ DRS (Dynamic Resource System)

Desenvolvido por E2S Labs

## 📌 Sobre o Projeto

O DRS é um ERP SaaS verticalizado de alta performance, projetado especificamente para o varejo de comunicação visual. Em vez de um sistema monolítico rígido, o DRS utiliza uma arquitetura orientada a eventos para reduzir o "arrasto operacional" de empresas com múltiplas filiais, integrando gestão transacional (Vendas, Estoque) com inteligência preditiva (Data/ML).

## 🏗️ Arquitetura (Monorepo)

Este repositório consolida todo o ecossistema DRS, estruturado sob o padrão de Monorepo para garantir consistência de deploy e versionamento atômico:
```
drs-erp/
├── backend/                # Laravel 11 Application (Core)
├── frontend/               # Nuxt 3 Application (SPA)
├── data-service/           # Python FastAPI Application (Analytics)
├── infra/                  # Manifestos IaC (Kubernetes, Docker, Prometheus)
├── docker-compose.yml      # Orquestração local de desenvolvimento
```

## 🛠️ Stack Tecnológica

Apresentação: Vue.js 3, Nuxt 3, TailwindCSS, Pinia.

Regra de Negócio: PHP 8.3, Laravel 11, Spatie Data/QueryBuilder.

Inteligência e Dados: Python 3.11, FastAPI, Polars, LangChain.

Persistência & Mensageria: PostgreSQL 16, Redis 7, Apache Kafka.

## 🚀 Como Iniciar (Ambiente de Desenvolvimento)

Para garantir paridade entre os ambientes da equipe, toda a infraestrutura local é orquestrada via Docker Compose.

Clone o repositório:
```bash
git clone git@github.com:e2s-labs/drs-erp.git
cd drs-erp
```

Copie as variáveis de ambiente:
```bash
cp .env.example .env
# Certifique-se de preencher as chaves de acesso no arquivo .env
```

Inicie a infraestrutura e os serviços:
```bash
docker-compose up -d --build
```

Acessos Locais:

Frontend (SPA): `http://localhost:3000`

Backend API: `http://localhost:8000`

Documentação da API (Swagger/Scramble): `http://localhost:8000/docs/api`

## 📚 Documentação e Diretrizes

Antes de iniciar qualquer desenvolvimento ou abrir um Pull Request, é obrigatória a leitura das diretrizes de engenharia do projeto.

[Documento de Arquitetura Técnica (TDD)](docs/technical-design-document.md)

[Padrões de Código e Conventional Commits]()

[Workflow de Git e Code Review (Regras da E2S Labs)]()

_Confidencial - Propriedade Intelectual da E2S Labs © 2026_