# 📗 Technical Design Document - D.R.S

**Versão:** 1.1.0 | **Status:** Ativo | **Última Atualização:** 23/02/2026
| **Público-Alvo:** Equipe de Engenharia e Desenvolvimento

## 1. Visão Executiva

O **D.R.S** (Dynamic Resource System) é uma plataforma SaaS verticalizada voltada ao varejo de comunicação visual. O objetivo deste documento é estabelecer as diretrizes arquiteturais, stack tecnológica e padrões de projeto que guiarão o desenvolvimento interno, garantindo alinhamento técnico, escalabilidade e manutenibilidade do código.

### 1.1 Objetivos de Engenharia

- **Desacoplamento:** Separação estrita entre camadas de apresentação (Front), regras de negócio (Back) e processamento analítico (Data).

- **Escalabilidade:** Arquitetura Cloud-Native baseada em containers e orquestração.

- **Consistência:** Adoção de tipagem rigorosa e padrões arquiteturais sólidos em toda a base de código.


## 2. Arquitetura Geral do Sistema

O sistema adota um modelo de **Arquitetura Orientada a Eventos (Event-Driven Architecture)**, segregando responsabilidades em serviços específicos.

- **Frontend (Apresentação):** Single Page Application (SPA) que atua unicamente como interface de utilizador, consumindo dados via chamadas HTTP síncronas.

- **Backend Core (Transacional):** Monolito que detém o domínio de negócio, validações transacionais, regras fiscais e controlo de estado do sistema. Expõe uma API RESTful.

- **Data Service (Analítico/Inteligência):** Microsserviço isolado, responsável pelo processamento assíncrono de dados massivos, inferência de modelos de Machine Learning e execução de Agentes (RAG).

- **Camada de Integração:** A comunicação síncrona ocorre via APIs REST. A comunicação assíncrona (ex: desacoplamento de rotinas pesadas entre o Core e o Data Service) ocorre através de um Message Broker.

### 2.1 Diagrama de Arquitetura de Alto Nível

![diagrama-drs.jpg](imgs/diagrama-drs.jpg)


## 3. Modelação de Dados e Isolamento Lógico (Multi-Filial)

Como o sistema é operado por uma matriz com múltiplas filiais, adotamos o padrão de **Hierarchical Data Isolation (Isolamento Multi-Filial)**, e NÃO um Multi-Tenancy estrito.

### 3.1. Estrutura de Base de Dados

Para entidades transacionais com isolamento por filial, o modelo deve considerar:

- `id` (UUID ou BigInt).

- `branch_id` (Chave estrangeira para `branches`).

- `created_at` / `updated_at`.

- `deleted_at` (Soft Delete obrigatório).

### 3.2. Estratégia de Isolamento

O isolamento, quando aplicado ao domínio, deve ser feito via **Contextual Scopes** aliado ao RBAC (`spatie/laravel-permission`):

- **Utilizadores Locais (Vendedores, Gerentes de Loja):** consultar apenas dados da filial permitida no contexto de autorização.

- **Utilizadores Globais (Diretoria, Backoffice):** podem ter visão consolidada quando houver permissão de escopo global.

- **Header de Contexto:** `X-Branch-Id` pode ser usado para explicitar contexto de filial quando o fluxo exigir.


## 4. Stack Tecnológica

### 4.1 Frontend

- **Nuxt 4 (Vue 3 + TypeScript):** Framework base para a SPA.

- **PrimeVue (Unstyled) + Tailwind CSS:** Sistema de componentes de UI complexos (DataTables, Trees) com estilização delegada ao Tailwind.

- **Pinia:** Gestão de estado global.

- **nuxt-auth-sanctum + useFetch:** base atual para autenticação e consumo HTTP.

### 4.2 Backend Core

- **Laravel 12 (PHP 8.4 em runtime Docker; `^8.2` no Composer):** framework base para a API REST transacional.

- **Sanctum:** Autenticação Stateless via Tokens.

- **Scramble:** Geração automatizada de documentação OpenAPI (Swagger).

- **Ecossistema Spatie:**

    - `laravel-query-builder`: Padronização de filtros e ordenações de API.

    - `laravel-permission`: Controlo de Acesso Baseado em Papéis (RBAC).

    - `laravel-medialibrary`: Processamento e associação de ficheiros/mídias.

### 4.3 Data Intelligence Service

- **Python 3.11+:** Runtime principal.

- **FastAPI:** Exposição de endpoints analíticos de alta performance.

- **Polars:** Processamento vetorizado de DataFrames.

- **LangChain + ChromaDB:** Orquestração de LLMs e base de dados vetorial para RAG.

### 4.4 Infraestrutura de Persistência

- **PostgreSQL 16:** Base de dados relacional primária (OLTP).

- **Redis 7:** Armazenamento em memória (Cache e Filas efémeras).

- **Apache Kafka:** Barramento de eventos distribuído (Mensageria assíncrona).

### 4.5 Infraestrutura e Observabilidade (DevOps)

A infraestrutura é tratada como código (IaC) e projetada para resiliência e monitorização contínua.

- **Docker / Docker Compose:** Padronização do empacotamento da aplicação e orquestração do ambiente de desenvolvimento local, garantindo paridade entre máquinas.

- **Kubernetes (K8s):** Orquestrador de containers para o ambiente de produção. Responsável pelo auto-scaling, self-healing e gestão de deployments.

- **Prometheus:** Ferramenta para recolha e armazenamento de métricas em séries temporais baseadas no modelo Pull. Atua como a fonte de verdade para a saúde da infraestrutura (uso de CPU, requisições HTTP, etc.).

- **Grafana:** Plataforma de visualização analítica, utilizada para construir painéis operacionais (dashboards) a partir dos dados agregados pelo Prometheus, facilitando a tomada de decisão técnica.

### 4.6 Links Úteis e Documentações Oficiais

**Atenção Desenvolvedores:** Consultem as documentações oficiais para entender as best practices de cada ferramenta antes da implementação.

- [Nuxt 4 Docs](https://nuxt.com/docs/4.x/getting-started/introduction) | [PrimeVue Docs](https://primevue.org/) | [Tailwind CSS Docs](https://tailwindcss.com/docs)

- [Laravel 12 Docs](https://laravel.com/docs/12.x) | [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) | [Spatie Laravel Query Builder](https://spatie.be/docs/laravel-query-builder) | [Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary) | [Scramble Docs](https://scramble.dedoc.co/)

- [FastAPI Docs](https://fastapi.tiangolo.com/) | [Pandas Docs](https://pandas.pydata.org/docs/) | [NumPy Docs](https://numpy.org/doc/)

- [PostgreSQL Docs](https://www.postgresql.org/docs/) | [Redis Docs](https://redis.io/docs/) | [Apache Kafka Docs](https://kafka.apache.org/documentation/) | [Docker Docs](https://docs.docker.com/) | [Kubernetes Docs](https://kubernetes.io/docs/)


## 5. Estrutura e Tipos de Ficheiros

Para manter a consistência no ecossistema da aplicação, definimos responsabilidades estritas para cada tipo de ficheiro no Backend e no Frontend.

### 5.1 Backend (Laravel)

O monolito mantém a estrutura padrão de diretórios do framework, porém com papéis arquiteturais definidos:

- **Routes (`routes/api.php`):** Define exclusivamente os endpoints e os verbos HTTP, delegando a requisição para o Controller correspondente.

- **Requests (`app/Http/Requests/`):** Classes de validação de formulários (FormRequests). Centralizam as regras de validação dos payloads de entrada e verificações de autorização primária.

- **Controllers (`app/Http/Controllers/`):** Atuam apenas como routers internos. Recebem a requisição validada, repassam para as Actions/Services e retornam uma resposta HTTP (via Resources). Não devem conter regras de negócio complexas.

- **Actions / Services (`app/Actions/` ou `app/Services/`):** Onde reside a lógica de negócio central (ex: `CreateUserAction`).

- **Resources (`app/Http/Resources/`):** Camada de transformação de saída. Mapeiam as Models do Eloquent para representações JSON (API Resources), garantindo contratos de API estáveis e omitindo dados sensíveis.

- **Models (`app/Models/`):** Representações das entidades da base de dados (Eloquent ORM). Devem conter apenas relacionamentos, mutators, casts e scopes.

- **Migrations (`database/migrations/`):** Controlo de versão do esquema da base de dados.

- **Jobs (`app/Jobs/`):** Classes que encapsulam lógicas a serem processadas de forma assíncrona através das filas de processamento (ex: envio de e-mails, emissão de relatórios).

### 5.2 Frontend (Nuxt/Vue)

A organização da SPA segue o sistema de roteamento baseado em ficheiros do Nuxt e segregação lógica de componentes:

- **Pages (`app/Pages/`):** Ficheiros que mapeiam diretamente para rotas de URL.

- **Components (`app/components/`):** Elementos de interface reutilizáveis.

- **Composables (`app/Composables/`):** Funções TypeScript para lógica reativa e integração com API (ex: `useUsers`, `useLoginForm`).

- **Services / Repositories (`app/services/`, quando necessário):** abstrações adicionais para chamadas HTTP quando os composables deixarem de ser suficientes.

- **Stores (`app/stores/`, quando necessário):** estado global com Pinia.

- **Layouts (`app/Layouts/`):** wrappers visuais da aplicação.


## 6. Padrões de Projeto (Design Patterns)

### 6.1 Backend (MVC com Actions)

A aplicação respeita a organização estrutural padrão do Laravel (Model-View-Controller), mas mitiga a deficiência de escalabilidade de Controllers pesados introduzindo uma camada de abstração para as regras de negócio:

- **Action Pattern:** O Controller atua como orquestrador HTTP e delega lógica para Actions/Services.

- **Eventos (Pub/Sub):** Desacoplamento de ações colaterais. Uma mutação transacional primária (ex: Venda Aprovada) dispara um evento (SaleApprovedEvent); listeners independentes assumem as tarefas secundárias de forma síncrona ou assíncrona na fila.

### 6.2 Frontend (Segregação por Domínio Visual)

O roteamento e a organização visual seguirão uma estrutura focada no domínio de negócio, evitando pastas tecnológicas genéricas (quando cabível) para garantir coesão.

- **Domain Pages:** As views/páginas podem ser agregadas por domínio (ex: `app/Pages/configuration/users/index.vue`).

- **Repository Pattern:** O acesso à API REST não é feito de forma crua nos componentes visuais. O isolamento de contratos ocorre nos Services, garantindo que, se o formato de um JSON mudar na API, apenas um ficheiro TypeScript precisará de correção.

## 7. Organização de Repositório (Monorepo)

O sistema operará sob um padrão de Monorepo para assegurar a atomicidade de commits que envolvem alterações em múltiplas frentes (ex: alteração de um contrato no Backend e seu consumo imediato no Frontend).

### 7.1 Estrutura de Diretórios Base

```
drs-erp/
├── backend/                # Laravel 12 Application (Core)
├── bi-service/             # Python FastAPI Application (Analytics)
├── docs/                   # Project Documentation (Markdown, Diagrams)
├── frontend/               # Nuxt 4 Application (SPA)
├── infra/                  # Manifestos IaC (Kubernetes, Prometheus, Grafana)
├── bin/                    # Scripts de automação local
├── Makefile                # CLI de comandos do projeto
├── docker-compose.yml      # Orquestração local de desenvolvimento
```

## 8. Futuras Implementações (Roadmap Técnico)

As seguintes tecnologias e arquiteturas estão mapeadas para inclusão futura conforme a maturidade e volume do sistema exigirem:

- **Loki (Log Aggregation):** Centralização e indexação de logs de todos os containers da infraestrutura, permitindo buscas transversais por eventos no ambiente orquestrado sem necessidade de acesso individual aos pods.

- **OpenTelemetry (Tracing Distribuído):** Implementação de rastreabilidade ponta a ponta. Permitirá acompanhar uma requisição desde a interação inicial do utilizador no Frontend (Nuxt), passando pela API (Laravel), até as consultas em bases de dados ou chamadas ao serviço Python (FastAPI).

- **Circuit Breakers / Service Mesh:** Proteção adicional para as chamadas de API internas contra falhas em cascata em momentos de alta latência sistémica.
