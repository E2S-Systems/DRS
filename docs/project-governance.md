# 📘 Project Governance - D.R.S

**Documento de Organização e Gestão do Projeto**

**Versão:** 1.0.0  
**Data de Criação:** 24/02/2026  
**Status:** Ativo  
**Responsável pela Manutenção:** Tech Lead do Projeto

---

# 1. Objetivo do Documento

Este documento define **padrões organizacionais, fluxo de trabalho, governança técnica e regras de colaboração** da equipe de desenvolvimento do ERP.

> ⚠️ Este documento NÃO substitui o Documento Técnico de Arquitetura.  
> Ele complementa, focando em organização da equipe e qualidade do processo.

---

# 2. Estrutura da Equipe

Equipe composta por **4 desenvolvedores nível Júnior/Estágio** com foco em:

- Evolução técnica
    
- Construção de portfólio
    
- Possível produto comercial futuro
    

## 2.1 Papéis Internos

Mesmo sendo um time pequeno, haverá papéis definidos:

|Papel|Responsabilidade|
|---|---|
|**Tech Lead (Rotativo ou Fixo)**|Decisão técnica final, revisão crítica, arquitetura|
|**Backend Developer**|Laravel, regras de negócio, API|
|**Frontend Developer**|Nuxt 4, UX, integração API|
|**Microservices Developer**|Serviços Python (processamentos, integrações, tarefas assíncronas)|

> Todos devem saber o básico de todas as camadas. A especialização não impede colaboração cruzada.

---

# 3. Stack Tecnológica Oficial

## 3.1 Backend Principal

|Camada|Tecnologia|Observação|
|---|---|---|
|Backend Core|Laravel 12|PHP 8.4 (runtime em Docker)|
|API Docs|Scramble (OpenAPI)|Contrato obrigatório|
|Database|PostgreSQL|Modelagem relacional robusta|
|Cache / Queue|Redis|Filas e jobs|
|Autenticação|Sanctum (token)|Padrão atual do projeto|

---

## 3.2 Frontend

|Camada|Tecnologia|
|---|---|
|Framework|Nuxt 4 (Vue 3)|
|Modo|SPA (SSR desabilitado inicialmente)|
|UI|PrimeVue|
|Estado Global|Pinia|

---

## 3.3 Microserviços

|Camada|Tecnologia|
|---|---|
|Linguagem|Python 3.11+|
|Framework sugerido|FastAPI|
|Comunicação|REST e mensageria via Kafka (quando aplicável)|
|Responsabilidade|Processamentos pesados, integrações externas, rotinas financeiras, geração de relatórios complexos|

### Regras Importantes

- Microserviço **não acessa banco diretamente sem contrato definido**.
    
- Comunicação sempre via API ou fila.
    
- Cada microserviço deve ser versionado separadamente.
    

---

# 4. Padrões de Código

---

## 4.1 Backend (Laravel)

### Regras obrigatórias:

- PSR-12
    
- `declare(strict_types=1);`
    
- Tipagem explícita em parâmetros e retornos
    
- Controllers com no máximo **15 linhas por método**
    
- Regra de negócio NÃO deve ficar em Controller
    

### Estrutura de responsabilidade:

|Camada|Responsabilidade|
|---|---|
|Model|Relacionamentos e Scopes|
|Request|Validação|
|Controller|Orquestração|
|Service/Action|Regra de negócio|
|Job|Processamento assíncrono|

---

## 4.2 Frontend (Nuxt 4)

- Uso obrigatório de `<script setup>`
    
- Componentes em `PascalCase`
    
- Separar:
    
    - `app/components/`
        
    - `app/Pages/`
        
    - `app/Composables/`
        
    - `app/stores/` (quando necessário)
        

### Regra crítica:

> Nenhuma tela começa a ser feita antes do contrato da API estar definido.

---

## 4.3 Microserviço Python

- PEP-8 obrigatório
    
- Tipagem com `typing`
    
- Uso de `pydantic` para validação
    
- No estado atual do repositório, o serviço está centralizado em `bi-service/main.py`.
        

---

# 5. Arquitetura Organizacional

---

## 5.1 Isolamento Multi-Filial

Estratégia atual: isolamento por filial para entidades que exigem escopo de unidade.

Para entidades com escopo por filial, as tabelas devem conter:

- `id`
    
- `branch_id`
    
- `created_at`
    
- `updated_at`
    
- `deleted_at`
    

### Regra Crítica

Nenhuma query de entidades com escopo filial deve ignorar `branch_id`.

---

# 6. Padrão de Commits

Utilizaremos prefixos padronizados.

## Estrutura:

```
prefixo: descrição curta
```

## Prefixos Permitidos:

- `feat` → Nova funcionalidade
    
- `fix` → Correção de bug
    
- `refactor` → Refatoração
    
- `docs` → Documentação
    
- `style` → Formatação
    
- `test` → Testes
    
- `chore` → Tarefas internas
    
- `perf` → Melhoria de performance
    
- `ci` → Configuração de CI/CD
    
- `build` → Mudanças de build ou dependências
    
- `revert` → Reversão de commit
    

Exemplo:

```
feat: adiciona cálculo automático de juros
```

---

# 7. Fluxo Oficial de Desenvolvimento

---

## 7.1 Regra Número 1

> Ninguém desenvolve nada sem um card no Jira.

---

## 7.2 Branch Strategy

Padrão adotado:

- `main` → Produção
    
- `develop` → Integração
    
- `<type>/<TICKET-ID>-<descricao-curta>`
- Exemplo: `feat/DRS-102-calculo-impostos`
- Exemplo: `fix/DRS-105-erro-login-nulo`
    

---

## 7.3 Pull Request

Obrigatório:

- Descrição clara
    
- Print ou evidência (se UI)
    
- Teste manual descrito
    
- Code Review de pelo menos 1 membro
    

---

# 8. Definition of Done (DoD)

Um card só pode ir para **DONE** se:

-  Código segue padrão definido
    
-  Isolamento multi-filial validado (quando aplicável)
    
-  API documentada
    
-  Revisado por outro membro
    
-  Sem warnings críticos
    
-  Sem TODOs esquecidos
    
-  Testes mínimos para regras críticas
    

---

# 9. Qualidade e Evolução Técnica

## 9.1 Code Review

Durante review avaliar:

- Legibilidade
    
- Responsabilidade única
    
- Complexidade
    
- Segurança
    
- Performance
    

---

## 9.2 Débito Técnico

Todo débito técnico deve:

- Ser registrado no Jira
    
- Ter label `tech-debt`
    
- Não pode ficar invisível
    

---

# 10. Versionamento do Documento

|Versão|Data|Alterações|
|---|---|---|
|1.0.0|24/02/2026|Criação do documento organizacional|

---

# 11. Cultura do Projeto

Este ERP não é apenas código.

É:

- Formação técnica
    
- Construção de maturidade
    
- Base para produto real
    
- Projeto que deve poder ser apresentado a investidor futuramente
    

Regras culturais:

- Sem ego.
    
- Sem código “por impulso”.
    
- Decisão técnica sempre documentada.
    
- Clareza > Complexidade.
    
- Consistência > Velocidade.
    

---
