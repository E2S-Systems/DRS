# Descrição

Explique de forma clara e objetiva o que foi feito nesta PR.

> Exemplo: Implementado CRUD de produtos com validações básicas e integração com estoque.

---

# Tipo de mudança

Marque o tipo da sua PR:

- [ ] Nova funcionalidade
- [ ] Correção de bug
- [ ] Refatoração
- [ ] Melhoria técnica
- [ ] Documentação
- [ ] Remoção de código
- [ ] Melhoria de performance

---

# Contexto

Qual problema essa PR resolve?

Se existir ticket no Jira, linke aqui:

Jira: DRS-XXX

---

# O que foi feito

Liste objetivamente as alterações:

- Criada migration `products`
- Criado Model `Product`
- Criado `ProductController`
- Criadas rotas API
- Implementada tela Vue de listagem
- Adicionadas validações no backend

---

# Como testar

Explique passo a passo como validar essa PR:

1. Rodar `make migrate`
2. Acessar `/produtos`
3. Criar novo produto
4. Validar listagem
5. Testar edição
6. Testar exclusão

---

# Pontos de atenção

Existe algo que o revisor deve observar?

- Regra de negócio específica?
- Decisão arquitetural?
- Algo que pode ser melhorado depois?

---

# Checklist do desenvolvedor

Antes de solicitar review, confirme:

- [ ] Código segue o padrão do projeto
- [ ] Não existem `dd()`, `dump()`, `console.log()` esquecidos
- [ ] Testado manualmente
- [ ] Não quebrou funcionalidades existentes
- [ ] Migration funciona corretamente
- [ ] Variáveis e métodos com nomes claros
- [ ] PR não está excessivamente grande

---

# Checklist do revisor

- [ ] Código está legível
- [ ] Responsabilidades estão bem separadas
- [ ] Não há lógica de negócio indevida no Controller
- [ ] Validações estão corretas
- [ ] Não há código duplicado
- [ ] Está de acordo com a arquitetura definida

---

# Observações finais

Caso necessário, adicione comentários adicionais aqui.
