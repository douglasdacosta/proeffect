# Análise do projeto para criação de documentação Swagger

Data da análise: 10/03/2026  
Projeto: `proeffect` (Laravel 9)

## 1) Resumo executivo

O projeto está apto para adotar Swagger/OpenAPI, mas hoje a API documentável está concentrada em **2 endpoints em `routes/api.php`**:

- `POST /api/salva-dados-maquina`
- `GET /api/get-horas-turno`

Não há pacote Swagger instalado no projeto neste momento (sem `l5-swagger`, `swagger-php` ou anotações `@OA`).

Além disso, existem diversos endpoints com comportamento de API em `routes/web.php` (muitos `POST` de AJAX), que podem entrar em uma **fase 2** de documentação caso a equipe queira incluir integrações internas.

---

## 2) Diagnóstico técnico atual

## Stack e base

- Framework: Laravel `^9.19`
- PHP mínimo: `^8.0.2`
- Autenticação de API já presente: Sanctum (`laravel/sanctum`)
- Prefixo padrão de API ativo (`/api`) via `RouteServiceProvider`

## Rotas API mapeadas

Arquivo `routes/api.php`:

1. `POST /api/salva-dados-maquina` -> `MaquinasController@salvaDadosMaquina`
2. `GET /api/get-horas-turno` -> `MaquinasController@getHorasTurno`

## Segurança atual dos endpoints de máquinas

Os métodos validam acesso via **credenciais no corpo da requisição**:

- `TOKEN`
- `LOGIN`
- `SENHA`

comparando com variáveis de ambiente (checagem no método `checkAcesso`).

> Observação importante: isso funciona, mas não segue o padrão OpenAPI mais comum de autenticação por `Bearer token`/`API Key` em header.

## Formatos de resposta atuais (impacto na documentação)

- Há respostas de sucesso/erro como string simples (`"sucesso"`, `"erro"`)
- Há resposta estruturada (array) em `getHorasTurno`
- Código 401 é usado para erro de identificação

Isso exige cuidado na especificação Swagger para representar respostas heterogêneas.

---

## 3) Estratégia recomendada de implantação Swagger

## Fase 1 (recomendada, baixo risco)

Documentar **somente** os endpoints de `routes/api.php` e publicar `/api/documentation`.

Vantagens:

- Entrega rápida de valor
- Baixo impacto no sistema legado
- Cria padrão para expansão futura

## Fase 2 (opcional)

Mapear endpoints AJAX de `routes/web.php` que são consumidos como API interna e decidir se:

- permanecem em `web.php` apenas como internos, ou
- migram gradualmente para `api.php` com padrão REST/JSON

---

## 4) Ferramental sugerido

Para Laravel 9, o caminho mais simples é:

- pacote `darkaonline/l5-swagger`
- geração de documentação via comando Artisan
- UI Swagger servida pela aplicação

### Comandos base

```bash
composer require darkaonline/l5-swagger
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
php artisan l5-swagger:generate
```

Após configuração, acesso esperado da UI:

- `/api/documentation`

---

## 5) Modelo de autenticação no OpenAPI (situação atual x ideal)

## Situação atual

Credenciais (`TOKEN`, `LOGIN`, `SENHA`) vêm no payload da requisição.

## Recomendação pragmática

1. **Curto prazo**: documentar exatamente como está (campos no `requestBody`/query) para não quebrar integrações.
2. **Médio prazo**: evoluir para `securitySchemes` com `apiKey` em header (ou `bearerAuth` com Sanctum), mantendo compatibilidade por um período.

---

## 6) Especificação inicial dos endpoints (para primeira versão)

## `POST /api/salva-dados-maquina`

### Campos de entrada observados

- `TOKEN` (string)
- `LOGIN` (string)
- `SENHA` (string)
- `NUMERO_CNC` (string/int)
- `stshUsageTimeHoursService` (number)
- `stsTraveledDistMetersService` (number)
- `stsNumJobsDoneService` (number/int)

### Respostas observadas

- `200`: `"sucesso"` ou `"erro"`
- `401`: `"Erro na identificação"`

## `GET /api/get-horas-turno`

### Entrada observada

- autenticação: `TOKEN`, `LOGIN`, `SENHA`
- `NUMERO_CNC`

### Resposta de sucesso (estrutura atual)

Objeto com campos como:

- `turno`
- `textoHorasTrabalhadas`
- `textoHorasUsinadas`
- `horasTrabalhadasq`
- `horasUsinadas`

### Respostas observadas

- `200`: objeto de dados do turno ou `"erro"`
- `401`: `"Erro na identificação"`

---

## 7) Ajustes recomendados antes/depois da documentação

## Prioridade alta

1. Padronizar resposta JSON (evitar mistura string/objeto):
   - ex.: `{ "success": true, "message": "sucesso" }`
2. Adicionar validação de request (`FormRequest`) para campos obrigatórios e tipos.
3. Remover credenciais hardcoded de fluxos de cliente e preferir header seguro.

## Prioridade média

1. Definir versionamento de API (`/api/v1`)
2. Padronizar nomenclatura dos campos (evitar mistura PT/EN e siglas sem descrição)
3. Cobrir erros de negócio com status HTTP semânticos (`422`, `404`, etc.)

---

## 8) Roteiro de implementação (sugestão prática)

1. Instalar `l5-swagger`.
2. Publicar config e ajustar diretórios de scan.
3. Criar documentação base (`Info`, `Server`, `Tags`, `SecuritySchemes`).
4. Documentar os 2 endpoints de máquinas.
5. Gerar documentação e validar no `/api/documentation`.
6. Ajustar inconsistências de resposta (se necessário).
7. Definir backlog da fase 2 para endpoints AJAX.

---

## 9) Critérios de pronto (DoD) para Swagger v1

- UI Swagger disponível no ambiente de desenvolvimento.
- Endpoints de `routes/api.php` visíveis e testáveis via “Try it out”.
- Esquemas de request/response alinhados com comportamento real.
- Erros 401 documentados.
- Guia rápido de atualização da documentação no README interno.

---

## 10) Riscos e cuidados

- **Risco de segurança**: exposição indevida de exemplo com credenciais reais.
  - Mitigação: usar placeholders nos exemplos Swagger.
- **Risco de divergência**: documentação desatualizar com mudanças de controller.
  - Mitigação: incluir geração no fluxo de deploy/CI ou checklist de release.
- **Risco funcional**: mudanças bruscas no contrato para clientes legados.
  - Mitigação: manter compatibilidade e versionar evolução.

---

## 11) Conclusão

O projeto já tem base suficiente para iniciar Swagger imediatamente com baixo impacto. A melhor abordagem é começar pelo escopo pequeno (`api.php`) e evoluir gradualmente para um padrão de API mais consistente (autenticação por header, respostas JSON padronizadas e versionamento).
