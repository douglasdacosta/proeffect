# Plano de Correção — Renovações não aparecem no Dashboard

## Sintoma relatado
Em produção, existem renovações que aparecem com alerta na tela **Renovações**
(`/renovacoes`), mas **não aparecem** no card "Renovações" do **Dashboard** (`/home`).

## Causa raiz

A regra de "está em alerta" é calculada de formas **diferentes** em dois lugares do
código, e o Dashboard usa uma regra mais restritiva que a tela de Renovações.

### 1. Tela de Renovações — [RenovacoesController.php](../app/Http/Controllers/RenovacoesController.php#L61-L83)
O alerta é `true` quando **qualquer uma** das condições abaixo ocorre:
- `inicio_renovacao` preenchido e **já chegou/passou** (`inicio_renovacao <= hoje`); **ou**
- `inicio_renovacao` vazio **e** `data_vencimento` já **vencida** (`data_vencimento < hoje`).

### 2. Dashboard — [HomeController.php](../app/Http/Controllers/HomeController.php#L71-L80)
```php
$renovacoes = Renovacoes::query()
    ->where('renovacoes.status', '=', 'P')
    ->where('renovacoes.data_vencimento', '<=', Carbon::now()->format('Y-m-d'))
    ->orderBy('renovacoes.data_vencimento', 'asc')
    ->orderBy('renovacoes.data_abertura', 'desc')
    ->limit(10)
    ->get();
```

Problemas nessa query, comparados à regra real de alerta:

1. **Ignora `inicio_renovacao`.** Uma renovação cujo `inicio_renovacao` já chegou (e por
   isso aparece em alerta na tela de Renovações) só entra no Dashboard se
   `data_vencimento` também já estiver vencida. Se `data_vencimento` estiver no futuro
   (ou vazia), ela nunca aparece no Dashboard, mesmo estando em alerta.
2. **`data_vencimento` nula é excluída.** Em SQL, `NULL <= 'data'` nunca é verdadeiro,
   então qualquer renovação sem `data_vencimento` preenchida (comum quando só se usa
   `inicio_renovacao`/`periodo_renovacao`) é descartada pelo `WHERE`, mesmo que esteja
   em alerta pela regra de `inicio_renovacao`.
3. **`limit(10)`** corta silenciosamente renovações adicionais quando há mais de 10
   registros em alerta — em produção, com mais dados acumulados, isso passa a ocorrer
   com mais frequência do que em ambiente de teste/local.

Ou seja, a divergência não é aleatória: qualquer renovação em alerta por causa de
`inicio_renovacao` (em vez de `data_vencimento`), ou sem `data_vencimento` preenchida,
ou que fique fora do "top 10", fica visível na tela de Renovações mas some do Dashboard.

## Impacto
- Renovações relevantes deixam de ser vistas pela gerência na tela inicial.
- Risco de renovação vencer sem que ninguém tenha sido notificado via Dashboard.

## Plano de ação

### Passo 1 — Centralizar a regra de "em alerta"
Extrair a lógica de cálculo de alerta (hoje duplicada apenas na tela) para um local único,
reutilizável tanto pelo `RenovacoesController::index` quanto pelo `HomeController::index`.
Sugestão: um método estático/scope no model `Renovacoes`.

```php
// app/Models/Renovacoes.php
public function scopeEmAlerta($query)
{
    $hoje = now()->startOfDay();

    return $query->where('status', 'P')
        ->where(function ($q) use ($hoje) {
            $q->where(function ($q2) use ($hoje) {
                $q2->whereNotNull('inicio_renovacao')
                    ->where('inicio_renovacao', '<=', $hoje);
            })->orWhere(function ($q2) use ($hoje) {
                $q2->whereNull('inicio_renovacao')
                    ->whereNotNull('data_vencimento')
                    ->where('data_vencimento', '<', $hoje);
            });
        });
}
```

### Passo 2 — Corrigir a consulta do Dashboard
Trocar o filtro atual pelo scope acima, mantendo ordenação e `limit`:

```php
if (in_array('6', $perfis_dashboards)) {
    $renovacoes = Renovacoes::query()
        ->select('renovacoes.*', 'perfis.nome as departamento_nome')
        ->leftJoin('perfis', 'perfis.id', '=', 'renovacoes.departamento_id')
        ->emAlerta()
        ->orderByRaw('renovacoes.data_vencimento IS NULL, renovacoes.data_vencimento asc')
        ->orderBy('renovacoes.inicio_renovacao', 'asc')
        ->limit(10)
        ->get();
}
```

### Passo 3 — Reutilizar o mesmo scope na tela de Renovações (opcional, mas recomendado)
Ajustar `RenovacoesController::index` para computar `em_alerta` reaproveitando a mesma
regra centralizada (mantendo o cálculo por item, já que a tela lista tanto alertas quanto
não-alertas), eliminando o risco de as duas regras voltarem a divergir no futuro.

### Passo 4 — Avaliar o `limit(10)`
Se em produção houver rotineiramente mais de 10 renovações em alerta, considerar:
- Aumentar o limite, ou
- Exibir um contador "+N outras renovações em alerta" com link para a tela de Renovações
  filtrada por `status=P`.

### Passo 5 — Testes
- Criar/atualizar testes (`tests/Feature`) cobrindo:
  - Renovação com `inicio_renovacao` no passado e `data_vencimento` no futuro → deve
    aparecer no Dashboard.
  - Renovação com `data_vencimento` nula e `inicio_renovacao` no passado → deve aparecer.
  - Renovação com `data_vencimento` vencida e `inicio_renovacao` vazio → deve aparecer
    (caso já coberto hoje).
  - Renovação finalizada (`status = F`) → nunca deve aparecer.
- Validar manualmente em produção comparando a contagem de "em alerta" na tela de
  Renovações com a lista exibida no Dashboard após o deploy.

### Passo 6 — Deploy
- Rodar migrations (nenhuma nova migration é necessária, mudança é só de query/model).
- Deploy da correção.
- Conferir no ambiente de produção os casos relatados pelo usuário que estavam sumindo.

## Arquivos a alterar
- [app/Models/Renovacoes.php](../app/Models/Renovacoes.php) — adicionar scope `emAlerta`.
- [app/Http/Controllers/HomeController.php](../app/Http/Controllers/HomeController.php#L71-L80) — usar o scope na consulta do dashboard.
- [app/Http/Controllers/RenovacoesController.php](../app/Http/Controllers/RenovacoesController.php#L61-L83) — (opcional) reaproveitar a mesma regra para cálculo de `em_alerta` por item.
- `tests/Feature` — novos testes cobrindo os cenários acima.
