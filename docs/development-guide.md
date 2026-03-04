# 📙 Development Guide - D.R.S

**Versão:** 1.0.0 | **Última Atualização:** 24/02/2026 | **Público-Alvo:** Equipe de Engenharia e Desenvolvimento

## 1. Padrões de Repositório e Versionamento

### 1.1. Estratégia e Nomenclatura de Branches

O fluxo segue um modelo _Trunk-Based / Git Flow_ simplificado. É terminantemente proibido realizar commits diretos nas branches main e develop.

- `main`: Código em produção.

- `develop`: Código de integração (ambiente de staging/teste).

**Nomenclatura de Branches de Trabalho:**

- Deve sempre referenciar o ID do ticket (Jira) e uma descrição curta em kebab-case.

- Padrão: `<tipo>/<TICKET-ID>-<descricao-curta>`

- Funcionalidades: `feat/DRS-102-calculo-impostos`

- Correções: `fix/DRS-105-erro-login-nulo`

- Débito Técnico: `tech-debt/DRS-110-refatorar-service-vendas`

### 1.2. Padrões de Commit (Conventional Commits)

O histórico de commits deve ser semântico e rastreável.

**Formato:** `tipo: descrição curta`

**Tipos permitidos:**

- `feat:` Nova funcionalidade.

- `fix:` Correção de falha (bug).

- `refactor:` Mudança de código que não altera comportamento (ex: aplicar design pattern).

- `docs:` Alteração apenas em documentação.

- `style:` Formatação, linting, remoção de espaços (não altera lógica).

- `test:` Adição ou correção de testes.

- `chore:` Atualização de dependências, configurações de build.

- `perf:` Melhoria de performance.

**Exemplo prático:**  `feat: adiciona calculo de juros no parcelamento`

### 1.3. Regras de Criação de PR

- **Jira Obrigatório:** Todo o PR deve ser originado de um cartão ativo no Jira. Ninguém programa sem um cartão.

- O título do PR deve refletir a funcionalidade entregue.

- A descrição deve conter instruções de como testar a funcionalidade localmente.

### 1.4. Checklist de Aprovação (Definition of Done - DoD)

Um PR só pode sofrer merge para a `develop` se cumprir os seguintes critérios obrigatórios:

- [ ] O código respeita os padrões de sintaxe e linting da respetiva stack.

- [ ] O isolamento multi-filial (`branch_id`) foi aplicado e testado nas queries.

- [ ] Nenhum aviso (_warning_) crítico de compilação ou consola foi introduzido.

- [ ] Notas temporárias e logs de debug (`dd()`, `console.log()`, `print()`) foram removidos.

- [ ] O contrato da API (no Backend) foi implementado e reflete no Swagger/Scramble.

- [ ] O PR recebeu Code Review e aprovação de pelo menos 1 (um) membro distinto da equipa.


## 2. Checklist Rápido: Criando um Novo CRUD

Sempre que uma nova entidade (ex: Produto, Fornecedor, Cliente) for desenvolvida, siga a ordem deste checklist para garantir a integridade da arquitetura do projeto.

### 2.1. Backend (Laravel)

- [ ] **Permissões (config/):** Atualizar permissions.php e mapear os acessos no profile-permissions.php. Rodar o Seeder em seguida.

- [ ] **Migration:** Criar a tabela. É obrigatório incluir branch_id (se for isolado por filial) e $table->softDeletes().

- [ ] **Seeder / Factory:** Criar dados de teste (mínimo de 10 registos) para facilitar a vida do Frontend.

- [ ] **Model:** Configurar $fillable, relacionamentos, casts e usar a Trait MultiTenantable (se aplicável ao domínio).

- [ ] **Policy:** Criar a Policy garantindo a união entre a permissão (Spatie) e o Isolamento Multi-Filial (branch_id).

- [ ] **FormRequests:** Criar classes de validação estritas para os verbos de entrada (ex: StoreProductRequest e UpdateProductRequest).

- [ ] **Service / Action:** Isolar a regra de negócio (inserção/atualização) num ficheiro dedicado (ex: ProductService).

- [ ] **Resource (DTO de Saída):** Criar a formatação de resposta (ex: ProductResource), ocultando campos sensíveis.

- [ ] **Controller:** Criar a classe apenas para orquestrar o Request, invocar o Service e retornar o Resource (máx. 15 linhas por método).

- [ ] **Rotas:** Adicionar o endpoint no routes/api.php, protegendo com o middleware de permissão ou authorize() no Controller.

### 3.2. Frontend (Nuxt/Vue)

- [ ] **Service de API (services/api/):** Criar a classe que espelha os endpoints gerados no Swagger/Scramble (ex: ProductService.ts).

- [ ] **Páginas (pages/):** Criar a estrutura de roteamento (ex: pages/products/index.vue, create.vue, [id].vue).

- [ ] **Componentes (components/):** Isolar elementos de UI complexos (ex: FormProduct.vue, DataTableProducts.vue). Eles devem ser "burros" (usar props e emits).

- [ ] **Store / Pinia (stores/):** Apenas se a entidade precisar de estar globalmente acessível na memória da aplicação (evitar o uso indiscriminado).

- [ ] **Layouts (layouts/):** Atualizar o menu lateral de navegação (se a nova entidade for uma rota principal).


## 3. Convenções de Código: Backend (Laravel)

### 3.1. Padrões Gerais e Tipagem

- Seguir estritamente o padrão **PSR-12**.

- A primeira linha de todo o ficheiro PHP deve conter: `declare(strict_types=1);`

- **Tipagem explícita obrigatória** para propriedades, parâmetros de métodos e retornos de funções.

- **Dinheiro:** Variáveis monetárias devem utilizar `BigInt` (cêntimos) ou `Decimal(15,2)`. É estritamente proibido o uso de `float` ou `double`.

### 3.2. Roteamento e Agrupamento

Agrupar rotas que partilham prefixos, middlewares ou controllers utilizando `Route::controller()` e `Route::group()`. Evitar declarar o mesmo controller repetidamente.
```php
Route::controller(OrderController::class)->prefix('orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
});
```

### 3.3. Nomenclatura CRUD de Controllers

Como a API é RESTful, os controllers devem seguir os 5 métodos padrão de `apiResource`. Não inventar verbos novos para operações de CRUD.

- `index:` Listagem de registos (com suporte a paginação/filtros).

- `show:` Exibição de um registo específico.

- `store:` Criação de um novo registo.

- `update:` Atualização total ou parcial de um registo.

- `destroy:` Exclusão (Soft Delete) de um registo.

### 3.4. Implementação Prática dos Design Patterns

- **Controllers:** A sua única responsabilidade é receber o `Request`, repassar os `DTOs` para o `Service/Action` e devolver o `Resource`.

- **Requests (FormRequests):** Toda a validação de input de utilizador deve ocorrer dentro de classes Request. Nunca validar diretamente no Controller.

- **Services / Actions:** Onde habita a regra de negócio real. Devem receber parâmetros estritamente tipados ou DTOs e realizar operações.

- **Models:** Devem conter apenas propriedades, relacionamentos, scopes locais (ex: isolamento de filial) e casts.

- **Eventos (Pub/Sub):** Utilizar eventos nativos para rotinas colaterais. (Ex: O `OrderService` guarda o pedido e dispara o `OrderCreatedEvent`. Um job assíncrono escuta e envia o e-mail).

## 4. Convenções de Código: Frontend (Nuxt/Vue)

### 4.1. Padrões Gerais

Uso obrigatório da Composition API com `<script setup>`. O uso de Options API é legado e não deve ser aplicado em código novo.

Utilizar TypeScript de forma agressiva (interfaces para propriedades e contratos de API).

### 4.2. Componentização e UI

- **Nomenclatura:** Ficheiros de componentes e páginas devem usar `PascalCase.vue` (ex: `DataTableProducts.vue`).

- **Componentes Burros (Dumb Components):** Componentes de interface (em `components/`) não devem fazer requisições à API. Devem receber dados via `props` e emitir ações via `emits`.

### 4.3. Consumo de API (Repository Pattern)

- Proibido o uso de chamadas HTTP (ex: `$fetch` ou `useFetch`) espalhadas diretamente pelos ficheiros `.vue`.

- As requisições devem ser encapsuladas em ficheiros dentro de `services/api/`. Isto garante um único ponto de manutenção caso o contrato da API mude.

### 4.4. Gestão de Estado Global (Pinia)

O Pinia `(stores/)` deve ser usado estritamente para estados globais da aplicação (ex: Dados do Utilizador logado, Filial ativa, Carrinho de Compras).

Estado transiente (ex: formulário em preenchimento, abertura de modal local) deve permanecer reativo apenas no escopo do componente utilizando `ref()` ou `reactive()`.

## 5. Convenções de Código: Microserviços (Python/FastAPI)

### 5.1. Padrões Gerais

Seguir as diretrizes **PEP-8** de forma estrita.

Uso obrigatório de tipagem nativa do Python via biblioteca `typing`.

Nomenclatura: `snake_case` para variáveis, métodos e ficheiros. `PascalCase` para Classes.

Separação lógica de diretórios: `routers/`, `services/`, `schemas/` e `core/`.

### 5.2. Fronteiras de Responsabilidade

Utilizar o `pydantic` para validação de Schemas de entrada e saída.

**Acesso a Dados:** O serviço analítico não deve aceder à base de dados transacional (PostgreSQL principal) para realizar mutações (INSERT/UPDATE). Todas as mutações do núcleo duro devem transitar via API REST ou mensagens no Kafka de volta ao Laravel.

## 6. Tratamento de Exceções e Erros

(Espaço reservado para futura definição arquitetural. Atualmente em análise pela equipa sobre a padronização de classes Exception globais, Handlers de interceção e formato de Payload de erro HTTP).

## 7. Regras e Cultura Transversais

### 7.1. Isolamento Multi-Filial

A plataforma D.R.S opera sob _Hierarchical Data Isolation._

O uso do `branch_id` é mandatório.

A filtragem deve ser aplicada no Repositório/Serviço utilizando as verificações de papéis (`spatie/laravel-permission`) para garantir que os utilizadores corporativos (Diretoria) conseguem visualizar dados consolidados.

### 7.2. Gestão de Débito Técnico

Código imperfeito escrito sob pressão de prazos é aceitável pontualmente, mas nunca deve ficar invisível. Todo o débito técnico inserido deve:

Ser registado no Jira imediatamente com a etiqueta `tech-debt`.

Conter um comentário no código seguindo o padrão: `// TODO: [DRS-XXX] Refatorar ...`